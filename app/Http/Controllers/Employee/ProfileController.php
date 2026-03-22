<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\TeamMemberDocumentRequest;
use App\Models\TeamMemberUpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $teamMember = $this->resolveTeamMember($request);

        $documents = $teamMember
            ? $teamMember->documents()->latest()->get()
            : collect();

        $documentCards = $this->buildDocumentCards($documents);
        $username = $user->email ? Str::before($user->email, '@') : ($user->name ?? 'employee');

        return view('employee.profile.show', [
            'user' => $user,
            'teamMember' => $teamMember,
            'username' => $username,
            'documentCards' => $documentCards,
            'documentsCount' => $documents->count(),
            'pendingUpdateRequest' => $teamMember?->pendingUpdateRequest,
            'pendingDocumentRequestsCount' => $teamMember ? $teamMember->pendingDocumentRequests()->count() : 0,
        ]);
    }

    public function submitUpdateRequest(Request $request)
    {
        $teamMember = $this->resolveTeamMember($request);
        if (! $teamMember) {
            return redirect()
                ->route('employee.profile.show')
                ->with('profile_error', 'Your account is not linked to an employee record yet.');
        }

        $validated = $request->validateWithBag('updateProfile', [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', Rule::in(['Onsite', 'Remote'])],
            'email' => ['required', 'email', 'max:255', Rule::unique('team_members', 'email')->ignore($teamMember->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp,gif'],
            'document_type' => ['nullable', 'string', 'max:100', 'required_with:documents'],
            'documents' => ['nullable', 'array'],
            'documents.*' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,gif,webp,bmp,svg,pdf,doc,docx,ppt,pptx,xls,xlsx,txt'],
        ]);

        $updatableFields = [
            'name',
            'role',
            'location',
            'email',
            'phone',
            'gender',
            'date_of_birth',
            'nationality',
            'address_line',
            'address_city',
            'address_state',
        ];

        $changes = [];

        foreach ($updatableFields as $field) {
            $newValue = $validated[$field] ?? null;
            $oldValue = data_get($teamMember, $field);

            if (is_string($newValue)) {
                $newValue = trim($newValue);
                $newValue = $newValue === '' ? null : $newValue;
            }

            if ($field === 'date_of_birth') {
                $newValue = $newValue ? Carbon::parse($newValue)->toDateString() : null;
                $oldValue = $oldValue ? Carbon::parse($oldValue)->toDateString() : null;
            }

            if ((string) ($oldValue ?? '') !== (string) ($newValue ?? '')) {
                $changes[$field] = $newValue;
            }
        }

        if ($request->hasFile('avatar')) {
            $storedPath = $request->file('avatar')->store('team-member-update-avatars/' . $teamMember->id, 'public');
            $changes['avatar'] = 'storage/' . ltrim($storedPath, '/');
        }

        $createdDocRequests = 0;
        $submittedDocs = $validated['documents'] ?? [];
        if (! empty($submittedDocs)) {
            $documentType = $this->normalizeDocumentType($validated['document_type'] ?? 'Document');

            foreach ($submittedDocs as $file) {
                $storedPath = $file->store('profile-document-requests/' . $teamMember->id, 'public');

                TeamMemberDocumentRequest::create([
                    'team_member_id' => $teamMember->id,
                    'name' => $file->getClientOriginalName(),
                    'size' => $this->formatBytes((int) $file->getSize()),
                    'type' => $documentType,
                    'path' => 'storage/' . ltrim($storedPath, '/'),
                    'status' => 'pending',
                ]);

                $createdDocRequests++;
            }
        }

        if (! empty($changes)) {
            $pendingRequest = TeamMemberUpdateRequest::query()
                ->where('team_member_id', $teamMember->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingRequest) {
                $pendingRequest->update([
                    'changes' => $changes,
                    'remarks' => null,
                    'reviewed_at' => null,
                ]);
            } else {
                TeamMemberUpdateRequest::create([
                    'team_member_id' => $teamMember->id,
                    'changes' => $changes,
                    'status' => 'pending',
                ]);
            }
        }

        if (empty($changes) && $createdDocRequests === 0) {
            return redirect()
                ->route('employee.profile.show')
                ->with('profile_warning', 'No changes detected to submit for approval.');
        }

        return redirect()
            ->route('employee.profile.show')
            ->with('profile_success', 'Update request submitted for admin approval.');
    }

    public function submitDocumentRequest(Request $request)
    {
        $teamMember = $this->resolveTeamMember($request);
        if (! $teamMember) {
            return redirect()
                ->route('employee.profile.show')
                ->with('profile_error', 'Your account is not linked to an employee record yet.');
        }

        $validated = $request->validateWithBag('submitDocument', [
            'document_type' => ['required', 'string', 'max:100'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,gif,webp,bmp,svg,pdf,doc,docx,ppt,pptx,xls,xlsx,txt'],
        ]);

        $documentType = $this->normalizeDocumentType($validated['document_type']);

        foreach ($validated['documents'] as $file) {
            $storedPath = $file->store('profile-document-requests/' . $teamMember->id, 'public');

            TeamMemberDocumentRequest::create([
                'team_member_id' => $teamMember->id,
                'name' => $file->getClientOriginalName(),
                'size' => $this->formatBytes((int) $file->getSize()),
                'type' => $documentType,
                'path' => 'storage/' . ltrim($storedPath, '/'),
                'status' => 'pending',
            ]);
        }

        return redirect()
            ->route('employee.profile.show')
            ->with('document_success', 'Document submission sent for admin approval.');
    }

    private function resolveTeamMember(Request $request): ?TeamMember
    {
        $user = $request->user();
        $email = strtolower(trim((string) ($user->email ?? '')));

        if ($email !== '') {
            $byEmail = TeamMember::whereRaw('LOWER(email) = ?', [$email])->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        $name = trim((string) ($user->name ?? ''));
        if ($name === '') {
            return null;
        }

        $matches = TeamMember::where('name', $name)->limit(2)->get();
        return $matches->count() === 1 ? $matches->first() : null;
    }

    private function buildDocumentCards(Collection $documents): Collection
    {
        return $documents
            ->groupBy(function ($doc) {
                return trim((string) ($doc->type ?? 'Document')) ?: 'Document';
            })
            ->map(function (Collection $docs, string $type) {
                $previewDoc = $docs->first(function ($doc) {
                    return $this->isImageFile((string) ($doc->path ?? ''), (string) ($doc->name ?? ''));
                }) ?? $docs->first();

                $previewUrl = $previewDoc ? $this->resolvePublicUrl((string) ($previewDoc->path ?? '')) : null;

                return [
                    'type' => $type,
                    'label' => Str::title(str_replace(['_', '-'], ' ', $type)),
                    'count' => $docs->count(),
                    'preview_url' => $previewUrl,
                    'preview_is_image' => $previewDoc
                        ? $this->isImageFile((string) ($previewDoc->path ?? ''), (string) ($previewDoc->name ?? ''))
                        : false,
                ];
            })
            ->values();
    }

    private function resolvePublicUrl(string $path): ?string
    {
        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }

    private function isImageFile(string $path, string $name = ''): bool
    {
        $value = strtolower($path . ' ' . $name);
        foreach (['.jpg', '.jpeg', '.png', '.gif', '.webp', '.bmp', '.svg'] as $ext) {
            if (Str::contains($value, $ext)) {
                return true;
            }
        }

        return false;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    private function normalizeDocumentType(string $type): string
    {
        $normalized = trim($type);
        return $normalized !== '' ? $normalized : 'Document';
    }
}

