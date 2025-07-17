<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
            'id_document_number' => 'required|string|max:255',
            'id_document_front' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'id_document_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'proof_of_address' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'date_of_birth', 'phone_number',
            'address', 'city', 'state', 'postal_code', 'country',
            'id_document_type', 'id_document_number'
        ]);

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

        KycVerification::create($data);

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
