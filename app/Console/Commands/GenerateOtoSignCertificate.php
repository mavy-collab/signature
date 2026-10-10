<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateOtoSignCertificate extends Command
{
    protected $signature = 'otosign:cert:generate {--force : Overwrite an existing certificate}';

    protected $description = 'Generate the self-signed platform certificate used to cryptographically seal signed PDFs';

    public function handle(): int
    {
        $certPath = config('services.otosign_pki.cert_path');
        $keyPath = config('services.otosign_pki.key_path');
        $p12Path = config('services.otosign_pki.p12_path');
        $passphrase = config('services.otosign_pki.passphrase') ?: '';

        if (! $this->option('force') && file_exists($certPath) && file_exists($keyPath)) {
            $this->info('A certificate already exists at '.$certPath.'. Use --force to regenerate it.');

            return self::SUCCESS;
        }

        $dir = dirname($certPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0700, true);
        }

        // openssl_csr_new()/openssl_csr_sign() need a real openssl.cnf on Windows or
        // they fail; point them at one explicitly rather than relying on php.ini/env.
        $opensslConfig = [];
        if ($cnf = $this->findOpensslConfig()) {
            $opensslConfig['config'] = $cnf;
        }

        $privateKey = openssl_pkey_new($opensslConfig + [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if ($privateKey === false) {
            $this->error('Could not generate a private key: '.openssl_error_string());

            return self::FAILURE;
        }

        $csr = openssl_csr_new([
            'countryName' => 'FR',
            'organizationName' => config('app.name'),
            'commonName' => config('app.name'),
        ], $privateKey, $opensslConfig);

        if ($csr === false) {
            $this->error('Could not generate a certificate signing request: '.openssl_error_string());

            return self::FAILURE;
        }

        $cert = openssl_csr_sign($csr, null, $privateKey, 3650, $opensslConfig, random_int(1, PHP_INT_MAX));

        if ($cert === false) {
            $this->error('Could not self-sign the certificate: '.openssl_error_string());

            return self::FAILURE;
        }

        if (! openssl_x509_export($cert, $certPem)) {
            $this->error('Could not export the certificate: '.openssl_error_string());

            return self::FAILURE;
        }

        if (! openssl_pkey_export($privateKey, $keyPem, $passphrase ?: null)) {
            $this->error('Could not export the private key: '.openssl_error_string());

            return self::FAILURE;
        }

        file_put_contents($certPath, $certPem);
        file_put_contents($keyPath, $keyPem);
        chmod($certPath, 0600);
        chmod($keyPath, 0600);

        openssl_pkcs12_export_to_file($cert, $p12Path, $privateKey, $passphrase);
        chmod($p12Path, 0600);

        if ($passphrase === '') {
            $this->warn('No OTOSIGN_CERT_PASSPHRASE set — the private key was written unencrypted. Set one in .env for production.');
        }

        $this->info('Platform certificate generated at '.$certPath);

        return self::SUCCESS;
    }

    private function findOpensslConfig(): ?string
    {
        foreach ([
            'C:\\xampp\\apache\\conf\\openssl.cnf',
            '/etc/ssl/openssl.cnf',
        ] as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
