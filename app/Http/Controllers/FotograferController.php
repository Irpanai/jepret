<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Photo;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\TransactionReporting;
use App\WithdrawalWorkflow;
use App\ProfilePhotoProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FotograferController extends Controller
{
    public function dashboard(Request $request, TransactionReporting $reporting): View
    {
        $user = $request->user()->load('package');
        $base = $reporting->query($request, $user->id);
        $paid = (clone $base)->paid();
        $sales = (clone $paid)->latest('paid_at')->paginate(5, ['*'], 'sales_page')->withQueryString();
        $visits = (int) Photo::where('fotografer_id', $user->id)->sum('views_count');
        $sold = (clone $paid)->count();
        $stats = [
            'earnings' => (int) ((clone $paid)->toBase()->selectRaw('COALESCE(SUM('.Transaction::photographerSql().'),0) AS total')->value('total')),
            'sold' => $sold, 'visits' => $visits, 'conversion' => $visits > 0 ? round($sold / $visits * 100, 1) : 0,
            'balance' => (int) $user->saldo, 'used_mb' => (float) $user->storage_terpakai_mb, 'quota_mb' => $user->effectiveQuotaMb(),
        ];
        $chart = $reporting->daily((clone $base)->where('transactions.created_at', '>=', now()->subDays(29)->startOfDay()));
        $withdrawals = Withdrawal::where('fotografer_id', $user->id)->latest()->limit(5)->get();

        return view('fotografer.dashboard', compact('user', 'stats', 'chart', 'sales', 'withdrawals'));
    }

    public function dashboardData(Request $request, TransactionReporting $reporting): JsonResponse
    {
        $query = $reporting->query($request, $request->user()->id)->paid();

        return response()->json(['latest_id' => (clone $query)->max('transactions.id'), 'paid_count' => (clone $query)->count(), 'balance' => $request->user()->fresh()->saldo]);
    }

    public function withdraw(Request $request, WithdrawalWorkflow $workflow): RedirectResponse
    {
        $data = $request->validate([
            'jumlah_tarik' => ['required', 'integer', 'min:1'], 'destination_type' => ['required', 'in:bank,ewallet'],
            'metode_pembayaran' => ['required', 'string', 'max:100'], 'nomor_tujuan' => ['required', 'string', 'max:100'],
            'account_holder' => ['nullable', 'string', 'max:150'], 'idempotency_key' => ['required', 'uuid'],
        ]);
        $workflow->submit($request->user(), $data);

        return back()->with('success', 'Permintaan penarikan dibuat.');
    }

    public function orders(Request $request, TransactionReporting $reporting): View
    {
        $query = $reporting->query($request, $request->user()->id);
        $transactions = $query->latest('transactions.created_at')->paginate(15)->withQueryString();

        return view('fotografer.orders', compact('transactions'));
    }

    public function exportOrders(Request $request, TransactionReporting $reporting): StreamedResponse
    {
        return $reporting->csv($reporting->query($request, $request->user()->id), 'jepret-orders-'.now()->format('Ymd').'.csv');
    }

    public function storage(Request $request): View
    {
        $user = $request->user()->load('package');
        $events = Event::where('fotografer_id', $user->id)->with(['photos:id,event_id,title,file_watermark,file_size_mb,storage_bytes'])->withCount('photos')->get();
        $folders = $events->map(fn ($event) => ['id' => $event->id, 'name' => $event->nama_event, 'count' => $event->photos_count, 'size' => round($event->photos->sum('storage_bytes') / 1048576, 2).' MB', 'photos' => $event->photos->map(fn ($photo) => ['id' => $photo->id, 'name' => $photo->title, 'size' => $photo->file_size_mb.' MB', 'url' => route('media.preview', $photo)])]);
        $storageTerpakai = $user->storage_terpakai_mb;
        $kuota = $user->effectiveQuotaMb();
        $package = $user->package;

        return view('fotografer.storage', compact('events', 'folders', 'storageTerpakai', 'kuota', 'package'));
    }

    public function portfolio(Request $request): View
    {
        $user = $request->user();
        $photos = Photo::where('fotografer_id', $user->id)->with('event')->latest('published_at')->paginate(12);

        return view('fotografer.portfolio', compact('user', 'photos'));
    }

    public function updatePortfolio(Request $request, ProfilePhotoProcessor $profilePhotoProcessor): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'studio_name' => ['nullable', 'string', 'max:255'], 'slug' => ['nullable', 'alpha_dash', 'max:255', 'unique:users,slug,'.$request->user()->id], 'whatsapp' => ['nullable', 'string', 'max:30'], 'location' => ['nullable', 'string', 'max:150'], 'category' => ['nullable', 'string', 'max:100'], 'bio' => ['nullable', 'string', 'max:1000'], 'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], 'profile_photo_zoom' => ['nullable', 'integer', 'between:100,200'], 'profile_photo_x' => ['nullable', 'integer', 'between:0,100'], 'profile_photo_y' => ['nullable', 'integer', 'between:0,100']]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']).'-'.$request->user()->id;
        unset($data['profile_photo'], $data['profile_photo_zoom'], $data['profile_photo_x'], $data['profile_photo_y']);
        if ($request->hasFile('profile_photo')) {
            $newProfilePhotoPath = $profilePhotoProcessor->process($request->file('profile_photo'), $request->integer('profile_photo_zoom', 100), $request->integer('profile_photo_x', 50), $request->integer('profile_photo_y', 50));
            if ($request->user()->profile_photo_path) {
                Storage::disk('public')->delete($request->user()->profile_photo_path);
            }
            $data['profile_photo_path'] = $newProfilePhotoPath;
        }
        $request->user()->forceFill($data)->save();

        return back()->with('success', 'Profil diperbarui.');
    }
}
