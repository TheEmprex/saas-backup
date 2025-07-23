<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Models\IdentityBlacklist;
use App\Services\DuplicateDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KycController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $kycVerification = $user->kycVerification;

        return view('theme::kyc.index', compact('kycVerification'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Check if user already has a KYC verification
        if ($user->hasKycSubmitted()) {
            return redirect()->route('kyc.index')
                ->with('error', 'You have already submitted your KYC verification.');
        }

        return view('theme::kyc.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Check if user already has a KYC verification
        if ($user->hasKycSubmitted()) {
            return redirect()->route('kyc.index')
                ->with('error', 'You have already submitted your KYC verification.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'id_document_type' => 'required|in:passport,driving_license,national_id',
            'id_document_number' => 'required|string|max:255|unique:kyc_verifications,id_document_number',
            'id_document_front' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'id_document_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'proof_of_address' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Update user's phone number if provided
        if (!$user->phone_number) {
            $user->update(['phone_number' => $request->phone_number]);
        }

        $data = $request->only([
            'first_name', 'last_name', 'date_of_birth', 'phone_number',
            'address', 'city', 'state', 'postal_code', 'country',
            'id_document_type', 'id_document_number'
        ]);

        // ===== SYSTÈME ANTI-DUPLICATE ULTRA-SOLIDE =====
        $duplicateService = new DuplicateDetectionService();
        
        // 1. Vérifier la blacklist
        $blacklistMatches = $duplicateService->checkBlacklist($data);
        if (!empty($blacklistMatches)) {
            Log::warning('KYC Blacklist detected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'blacklist_matches' => $blacklistMatches,
                'ip' => $request->ip()
            ]);
            
            return back()->withErrors([
                'security' => 'Your KYC application cannot be processed. Please contact support if you believe this is an error.'
            ])->withInput();
        }

        // 2. Détecter les doublons
        $duplicateReport = $duplicateService->generateDuplicateReport($user, $data);
        
        // Log the duplicate analysis
        Log::info('KYC Duplicate Analysis', $duplicateReport);

        // Handle file uploads
        if ($request->hasFile('id_document_front')) {
            $data['id_document_front_path'] = $this->storeFile($request->file('id_document_front'), 'kyc/id_documents');
        }

        if ($request->hasFile('id_document_back')) {
            $data['id_document_back_path'] = $this->storeFile($request->file('id_document_back'), 'kyc/id_documents');
        }

        if ($request->hasFile('selfie')) {
            $data['selfie_path'] = $this->storeFile($request->file('selfie'), 'kyc/selfies');
        }

        if ($request->hasFile('proof_of_address')) {
            $data['proof_of_address_path'] = $this->storeFile($request->file('proof_of_address'), 'kyc/proof_of_address');
        }

        $data['user_id'] = $user->id;
        $data['submitted_at'] = Carbon::now();
        
        // 3. Déterminer le statut initial basé sur l'analyse des doublons
        $recommendation = $duplicateReport['recommendation'];
        
        if (str_contains($recommendation, 'REJECT')) {
            // Rejeter immédiatement et blacklist automatiquement
            $data['status'] = 'rejected';
            $data['rejection_reason'] = 'Duplicate account detected: ' . $recommendation;
            $data['reviewed_at'] = now();
            $data['reviewed_by'] = 1; // System auto-review
            
            // Blacklist this user's data immediately
            IdentityBlacklist::blacklistUser($user, $recommendation, 1);
            
            Log::warning('KYC Auto-rejected for duplicates', [
                'user_id' => $user->id,
                'reason' => $recommendation,
                'duplicate_report' => $duplicateReport
            ]);
            
        } elseif (str_contains($recommendation, 'REQUIRES_REVIEW')) {
            // Marquer pour révision manuelle obligatoire
            $data['status'] = 'requires_review';
            
        } elseif (str_contains($recommendation, 'FLAG')) {
            // Statut pending mais flaggé pour attention
            $data['status'] = 'pending';
            
        } else {
            // Pas de problèmes détectés
            $data['status'] = 'pending';
        }

        $kyc = KycVerification::create($data);

        // Si rejeté automatiquement, rediriger avec message d'erreur
        if ($kyc->status === 'rejected') {
            return redirect()->route('kyc.index')
                ->with('error', 'Your KYC verification has been rejected. Multiple accounts are not allowed on this platform.');
        }

        return redirect()->route('kyc.index')
            ->with('success', 'Your KYC verification has been submitted successfully. We will review it within 24-48 hours.');
    }

    public function show(KycVerification $kyc)
    {
        // Only allow users to view their own KYC or admins
        if ($kyc->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('theme::kyc.show', compact('kyc'));
    }

    private function storeFile($file, $directory)
    {
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $fileName, 'private');
        return $path;
    }

    public function downloadFile($type, $id)
    {
        $kyc = KycVerification::findOrFail($id);
        
        // Only allow users to download their own files or admins
        if ($kyc->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $filePath = match ($type) {
            'id_front' => $kyc->id_document_front_path,
            'id_back' => $kyc->id_document_back_path,
            'selfie' => $kyc->selfie_path,
            'proof_address' => $kyc->proof_of_address_path,
            default => null
        };

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404);
        }

        return Storage::disk('private')->download($filePath);
    }
}
