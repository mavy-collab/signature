<?php

namespace App\Http\Controllers;

use App\Models\DocumentAuditLog;
use App\Models\SignatureAuditLog;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HistoryController extends Controller
{
    private const PER_PAGE = 20;

    /**
     * Show a unified, chronological trace of everything the account has done:
     * signature changes and document lifecycle events.
     */
    public function index(Request $request): View
    {
        $events = self::eventsFor($request->user()->id);

        $page = $request->integer('page', 1);
        $slice = $events->slice(($page - 1) * self::PER_PAGE, self::PER_PAGE)->values();

        $paginator = new LengthAwarePaginator(
            $slice,
            $events->count(),
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('history.index', ['events' => $paginator]);
    }

    /**
     * Build the full, chronological (most recent first) list of a user's signature and
     * document events. Shared by the history page (paginated) and the dashboard's
     * "recent activity" preview (taken as the first few).
     */
    public static function eventsFor(int $userId): Collection
    {
        $signatureEvents = SignatureAuditLog::where('user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(fn (SignatureAuditLog $log) => [
                'action' => $log->action,
                'label' => match ($log->action) {
                    'created' => __('Signature créée'),
                    'updated' => __('Signature mise à jour'),
                    'deleted' => __('Signature supprimée'),
                    default => $log->action,
                },
                'created_at' => $log->created_at,
                'route' => 'signature.show',
                'route_params' => [],
            ]);

        $documentEvents = DocumentAuditLog::where('user_id', $userId)
            ->with('document')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (DocumentAuditLog $log) => $log->document !== null)
            ->map(function (DocumentAuditLog $log) {
                $name = $log->document->original_name;

                return [
                    'action' => $log->action,
                    'label' => match ($log->action) {
                        'uploaded' => __('Document déposé : :name', ['name' => $name]),
                        'uploaded_signed' => __('Document déposé déjà signé : :name', ['name' => $name]),
                        'signed' => __('Document signé : :name', ['name' => $name]),
                        'vault_viewed' => __('Consultation dans le coffre-fort : :name', ['name' => $name]),
                        'downloaded' => __('PDF téléchargé : :name', ['name' => $name]),
                        default => $log->action.' : '.$name,
                    },
                    'created_at' => $log->created_at,
                    'route' => $log->document->isSigned() ? 'vault.show' : 'documents.show',
                    'route_params' => [$log->document],
                ];
            });

        return $signatureEvents->concat($documentEvents)
            ->sortByDesc('created_at')
            ->values();
    }
}
