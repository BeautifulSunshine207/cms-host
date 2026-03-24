@extends('employee.layouts.app')

@section('content')
@php
    $avatarPath = $teamMember?->avatar ? ltrim($teamMember->avatar, '/') : null;
    $avatarUrl = $avatarPath ? asset($avatarPath) : asset('images/logo-cms.png');
    $birthDate = $teamMember?->date_of_birth ? \Carbon\Carbon::parse($teamMember->date_of_birth)->format('F jS, Y') : '-';
    $maskedPassword = str_repeat('*', 11);
@endphp

<style>
    .employee-profile-page {
        min-height: 100vh;
        background: #dedede;
        padding: 22px 26px;
        font-family: "Inter", sans-serif;
    }

    .employee-profile-card {
        max-width: 1200px;
        margin: 0 auto;
        background: #f7f7f7;
        border-radius: 26px;
        padding: 34px 40px 48px;
    }

    .employee-profile-grid {
        display: grid;
        grid-template-columns: minmax(520px, 1fr) minmax(360px, 430px);
        column-gap: 48px;
        align-items: start;
    }

    .ep-title {
        color: #4f86f7;
        font-size: 15px;
        line-height: 1.2;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin: 0 0 12px;
    }

    .ep-top-row {
        display: grid;
        grid-template-columns: 180px 1fr;
        column-gap: 22px;
        margin-bottom: 18px;
        align-items: start;
    }

    .ep-photo-wrap {
        width: 180px;
    }

    .ep-photo-frame {
        width: 180px;
        height: 220px;
        border-radius: 22px;
        overflow: hidden;
        background: #d6d6d6;
    }

    .ep-photo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ep-photo-pill {
        width: 156px;
        margin: -18px auto 0;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.97);
        text-align: center;
        font-size: 12px;
        font-weight: 500;
        color: #1c1c1c;
        padding: 6px 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }

    .ep-main-info {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 6px;
    }

    .ep-field-label {
        font-size: 11px;
        color: #8a8a8a;
        line-height: 1.2;
        margin-bottom: 2px;
    }

    .ep-field-value {
        font-size: 15px;
        line-height: 1.25;
        color: #131313;
        font-weight: 600;
    }

    .ep-bottom-grid {
        margin-top: 10px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px 36px;
    }

    .ep-subtitle {
        color: #4f86f7;
        font-size: 15px;
        line-height: 1.2;
        font-weight: 600;
        letter-spacing: 0.02em;
        margin: 0 0 8px;
    }

    .ep-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ep-change-btn {
        margin-top: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        border-radius: 10px;
        background: #ffd200;
        color: #111;
        padding: 8px 14px;
        font-size: 12px;
        line-height: 1;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .ep-change-btn span {
        font-size: 13px;
        font-weight: 600;
    }

    .ep-change-btn-center {
        justify-content: center;
        text-align: center;
        min-width: 180px;
    }

    .ep-change-btn[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .ep-tier-wrap {
        margin-top: 12px;
    }

    .ep-tier-list {
        list-style: none;
        margin: 8px 0 0;
        padding: 0 0 0 18px;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ep-tier-list::before {
        content: "";
        position: absolute;
        left: 3px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: #d0d0d0;
    }

    .ep-tier-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #9a9a9a;
        line-height: 1.2;
    }

    .ep-tier-item::before {
        content: "";
        position: absolute;
        left: -18px;
        top: 50%;
        width: 8px;
        height: 8px;
        margin-top: -4px;
        border-radius: 999px;
        background: #c7c7c7;
    }

    .ep-tier-item.current {
        color: #111;
        font-weight: 600;
    }

    .ep-tier-item.current::before {
        background: #111;
    }

    .ep-tier-current {
        border: 1px solid #8b8b8b;
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 10px;
        line-height: 1;
        color: #333;
        background: #f5f5f5;
    }

    .ep-right .ep-title {
        margin-bottom: 16px;
    }

    .ep-doc-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .ep-doc-card {
        height: 215px;
        border-radius: 22px;
        overflow: hidden;
        position: relative;
        background: #d5d5d5;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .ep-doc-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ep-doc-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6a6a6a;
        font-size: 12px;
        font-weight: 600;
        background: linear-gradient(140deg, #dddddd, #c8c8c8);
    }

    .ep-doc-pill {
        position: absolute;
        left: 50%;
        bottom: 12px;
        transform: translateX(-50%);
        min-width: 220px;
        max-width: calc(100% - 32px);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.97);
        padding: 7px 62px 7px 14px;
        text-align: center;
        color: #222;
        font-size: 12px;
        font-weight: 600;
        line-height: 1;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ep-doc-pill-count {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: 1px solid #8d8d8d;
        border-radius: 999px;
        background: #f7f7f7;
        color: #333;
        font-size: 10px;
        font-weight: 600;
        line-height: 1;
        padding: 3px 8px;
    }

    .ep-alert {
        margin-bottom: 10px;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 500;
    }

    .ep-alert-success {
        background: #ecfdf3;
        border: 1px solid #86efac;
        color: #166534;
    }

    .ep-alert-warn {
        background: #fefce8;
        border: 1px solid #fde047;
        color: #854d0e;
    }

    .ep-alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }

    .ep-doc-actions {
        margin-top: 12px;
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 1200px) {
        .employee-profile-card {
            padding: 26px 24px 36px;
        }
        .employee-profile-grid {
            grid-template-columns: 1fr;
            row-gap: 26px;
        }
        .ep-doc-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 760px) {
        .ep-top-row {
            grid-template-columns: 1fr;
            row-gap: 16px;
        }
        .ep-photo-wrap {
            width: 100%;
            max-width: 200px;
        }
        .ep-photo-frame {
            width: 100%;
            height: 240px;
        }
        .ep-bottom-grid {
            grid-template-columns: 1fr;
            gap: 22px;
        }
    }
</style>

<div class="employee-profile-page">
    <div class="employee-profile-card">
        @if(session('status') === 'password-updated')
            <div class="ep-alert ep-alert-success">Password updated successfully.</div>
        @endif

        @if(session('profile_success'))
            <div class="ep-alert ep-alert-success">{{ session('profile_success') }}</div>
        @endif

        @if(session('document_success'))
            <div class="ep-alert ep-alert-success">{{ session('document_success') }}</div>
        @endif

        @if(session('profile_warning'))
            <div class="ep-alert ep-alert-warn">{{ session('profile_warning') }}</div>
        @endif

        @if(session('profile_error'))
            <div class="ep-alert ep-alert-error">{{ session('profile_error') }}</div>
        @endif

        @if($pendingUpdateRequest)
            <div class="ep-alert ep-alert-warn">You already have a pending profile update request awaiting admin approval.</div>
        @endif

        @if(($pendingDocumentRequestsCount ?? 0) > 0)
            <div class="ep-alert ep-alert-warn">You have {{ $pendingDocumentRequestsCount }} document request(s) waiting for admin approval.</div>
        @endif

        @if(!$teamMember)
            <div class="ep-alert ep-alert-warn">
                Your account is not linked to an employee record yet. Contact admin to complete your profile.
            </div>
        @endif

        <div class="employee-profile-grid">
            <section class="ep-left">
                <h2 class="ep-title">Personal Details</h2>

                <div class="ep-top-row">
                    <div class="ep-photo-wrap">
                        <div class="ep-photo-frame">
                            <img src="{{ $avatarUrl }}" alt="Verification Photo">
                        </div>
                        <div class="ep-photo-pill">Verification Photo</div>
                    </div>

                    <div class="ep-main-info">
                        <div>
                            <div class="ep-field-label">Name</div>
                            <div class="ep-field-value">{{ $teamMember?->name ?? $user->name }}</div>
                        </div>
                        <div>
                            <div class="ep-field-label">Gender</div>
                            <div class="ep-field-value">{{ $teamMember?->gender ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="ep-field-label">Date of Birth</div>
                            <div class="ep-field-value">{{ $birthDate }}</div>
                        </div>
                        <div>
                            <div class="ep-field-label">Role</div>
                            <div class="ep-field-value">{{ $teamMember?->role ?? 'Employee' }}</div>
                        </div>
                    </div>
                </div>

                <div class="ep-bottom-grid">
                    <div>
                        <h3 class="ep-subtitle">Personal</h3>
                        <div class="ep-stack">
                            <div>
                                <div class="ep-field-label">Employee Number</div>
                                <div class="ep-field-value">{{ $teamMember?->id ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="ep-field-label">Username</div>
                                <div class="ep-field-value">{{ $username }}</div>
                            </div>
                            <div>
                                <div class="ep-field-label">Password</div>
                                <div class="ep-field-value">{{ $maskedPassword }}</div>
                            </div>

                            <button type="button" class="ep-change-btn ep-change-btn-center" onclick="openPasswordModal()">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 17.25V21h3.75L18.81 8.94l-3.75-3.75L3 17.25Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="m14.06 4.19 3.75 3.75 1.77-1.77a1.25 1.25 0 0 0 0-1.77L17.6 2.42a1.25 1.25 0 0 0-1.77 0l-1.77 1.77Z" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <span>Change Password</span>
                            </button>

                            <button
                                type="button"
                                class="ep-change-btn ep-change-btn-center"
                                onclick="openUpdateProfileModal()"
                                {{ $teamMember ? '' : 'disabled' }}
                            >
                                <span>Update Profile</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <h3 class="ep-subtitle">Contact Details</h3>
                        <div class="ep-stack">
                            <div>
                                <div class="ep-field-label">Phone Number</div>
                                <div class="ep-field-value">{{ $teamMember?->phone ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="ep-field-label">Email</div>
                                <div class="ep-field-value">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="ep-tier-wrap">
                            <h3 class="ep-subtitle">Account Tier</h3>
                            <ul class="ep-tier-list">
                                @foreach(['admin' => 'Admin', 'employee' => 'Employee', 'owner' => 'Owner'] as $tier => $label)
                                    @php $current = strtolower((string) $user->role) === $tier; @endphp
                                    <li class="ep-tier-item {{ $current ? 'current' : '' }}">
                                        <span>{{ $label }}</span>
                                        @if($current)
                                            <span class="ep-tier-current">Current</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <section class="ep-right">
                <h2 class="ep-title">Submitted Documents</h2>

                <div class="ep-doc-list">
                    @forelse($documentCards as $card)
                        <article class="ep-doc-card">
                            @if($card['preview_url'] && $card['preview_is_image'])
                                <img src="{{ $card['preview_url'] }}" alt="{{ $card['label'] }}">
                            @else
                                <div class="ep-doc-fallback">Preview not available</div>
                            @endif

                            <div class="ep-doc-pill">
                                {{ $card['label'] }}
                                <span class="ep-doc-pill-count">
                                    {{ $card['count'] }} {{ $card['count'] === 1 ? 'File' : 'Files' }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <article class="ep-doc-card">
                            <div class="ep-doc-fallback">No submitted documents yet</div>
                            <div class="ep-doc-pill">
                                Documents
                                <span class="ep-doc-pill-count">0 File</span>
                            </div>
                        </article>
                    @endforelse
                </div>

                <div class="ep-doc-actions">
                    <button
                        type="button"
                        class="ep-change-btn ep-change-btn-center"
                        onclick="openDocumentModal()"
                        {{ $teamMember ? '' : 'disabled' }}
                    >
                        <span>Submit Document</span>
                    </button>
                </div>
            </section>
        </div>
    </div>
</div>

<div id="passwordModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/55" onclick="closePasswordModal()"></div>

    <div class="relative flex h-full items-center justify-center p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Change Password</h3>
                <button type="button" onclick="closePasswordModal()" class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-[#3b82f6]" required>
                    @if($errors->updatePassword->has('current_password'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-[#3b82f6]" required>
                    @if($errors->updatePassword->has('password'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-[#3b82f6]" required>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closePasswordModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-[#ffd400] px-5 py-2 text-sm font-semibold text-black hover:brightness-95">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="updateProfileModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/55" onclick="closeUpdateProfileModal()"></div>

    <div class="relative flex h-full items-center justify-center p-4">
        <div class="w-full max-w-4xl rounded-2xl bg-white p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Update Profile Request</h3>
                <button type="button" onclick="closeUpdateProfileModal()" class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($errors->updateProfile->any())
                <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc ml-5">
                        @foreach($errors->updateProfile->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.profile.submit-update-request') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $teamMember?->name ?? $user->name) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Role</label>
                        <input type="text" name="role" value="{{ old('role', $teamMember?->role) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Location</label>
                        <select name="location" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                            @php $oldLocation = old('location', $teamMember?->location); @endphp
                            <option value="">Select location</option>
                            <option value="Onsite" {{ $oldLocation === 'Onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="Remote" {{ $oldLocation === 'Remote' ? 'selected' : '' }}>Remote</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $teamMember?->email ?? $user->email) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $teamMember?->phone) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Gender</label>
                        <input type="text" name="gender" value="{{ old('gender', $teamMember?->gender) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $teamMember?->date_of_birth ? \Carbon\Carbon::parse($teamMember->date_of_birth)->toDateString() : '') }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Nationality</label>
                        <input type="text" name="nationality" value="{{ old('nationality', $teamMember?->nationality) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Address Line</label>
                        <input type="text" name="address_line" value="{{ old('address_line', $teamMember?->address_line) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="address_city" value="{{ old('address_city', $teamMember?->address_city) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="address_state" value="{{ old('address_state', $teamMember?->address_state) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">Verification Photo (Optional)</label>
                        <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp,.gif" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900">Optional Document Update (also sent for admin approval)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Document Type</label>
                            <input type="text" name="document_type" value="{{ old('document_type') }}" placeholder="e.g. Resume, ID, Certificate" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700">Documents</label>
                            <input type="file" name="documents[]" multiple class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeUpdateProfileModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-[#ffd400] px-5 py-2 text-sm font-semibold text-black hover:brightness-95">
                        Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="documentModal" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/55" onclick="closeDocumentModal()"></div>

    <div class="relative flex h-full items-center justify-center p-4">
        <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Submit Document</h3>
                <button type="button" onclick="closeDocumentModal()" class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @if($errors->submitDocument->any())
                <div class="mb-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-700">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc ml-5">
                        @foreach($errors->submitDocument->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('employee.profile.submit-document-request') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-700">Document Type</label>
                    <input type="text" name="document_type" value="{{ old('document_type') }}" placeholder="e.g. Resume, ID, Certificate" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Select Document(s)</label>
                    <input type="file" name="documents[]" multiple class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2" required>
                    <p class="mt-1 text-xs text-gray-500">Allowed: image, PDF, DOC, DOCX, PPT, XLS, TXT. Max 15MB each.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeDocumentModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="rounded-lg bg-[#ffd400] px-5 py-2 text-sm font-semibold text-black hover:brightness-95">
                        Submit for Approval
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openPasswordModal() { openModal('passwordModal'); }
    function closePasswordModal() { closeModal('passwordModal'); }

    function openUpdateProfileModal() { openModal('updateProfileModal'); }
    function closeUpdateProfileModal() { closeModal('updateProfileModal'); }

    function openDocumentModal() { openModal('documentModal'); }
    function closeDocumentModal() { closeModal('documentModal'); }

    @if($errors->updatePassword->any())
        openPasswordModal();
    @endif

    @if($errors->updateProfile->any())
        openUpdateProfileModal();
    @endif

    @if($errors->submitDocument->any())
        openDocumentModal();
    @endif
</script>
@endsection

