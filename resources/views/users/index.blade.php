@extends('layouts.app', ['title' => 'User Management'])

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-brand-navy">User & Role Management</h2>
            <p class="text-xs text-brand-secondary mt-0.5">Administer accounts, role privileges, and customer organizations</p>
        </div>

        <button type="button" onclick="document.getElementById('newUserModal').classList.remove('hidden')"
                class="px-4 py-2 bg-brand-primary hover:bg-brand-primary-hover text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
            + Create New User
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-brand-border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-brand-bg/60 text-brand-secondary border-b border-brand-border text-[11px] font-bold uppercase">
                    <tr>
                        <th class="py-3 px-6">Name / Organization</th>
                        <th class="py-3 px-6">Email Address</th>
                        <th class="py-3 px-6">System Role</th>
                        <th class="py-3 px-6">Shipments</th>
                        <th class="py-3 px-6">Created At</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border text-brand-navy">
                    @forelse($users as $u)
                    <tr class="hover:bg-brand-bg/30">
                        <td class="py-3.5 px-6 font-semibold">
                            {{ $u->name }}
                            @if($u->company_name)
                                <span class="text-[10px] text-brand-secondary block font-normal">{{ $u->company_name }}</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 font-mono text-[11px] text-brand-secondary">{{ $u->email }}</td>
                        <td class="py-3.5 px-6">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($u->role === 'operator' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-brand-primary') }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 font-semibold">{{ $u->shipments_count ?? 0 }}</td>
                        <td class="py-3.5 px-6 text-brand-secondary font-mono text-[11px]">{{ $u->created_at->format('Y-m-d') }}</td>
                        <td class="py-3.5 px-6 text-right space-x-2">
                            <button type="button" onclick="openEditUserModal({{ json_encode($u) }})"
                                    class="text-brand-primary hover:underline font-semibold">
                                Edit
                            </button>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user account?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-brand-danger hover:underline font-semibold">Delete</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-brand-secondary">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-brand-border bg-brand-bg/20">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal 1: Create New User -->
<div id="newUserModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Create System User</h3>
            <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Role</label>
                    <select name="role" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="customer">Customer</option>
                        <option value="operator">Logistics Operator</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Password</label>
                    <input type="password" name="password" minlength="8" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Company / Organization</label>
                <input type="text" name="company_name" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('newUserModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Edit User -->
<div id="editUserModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-brand-border max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-brand-border pb-2">
            <h3 class="text-base font-bold text-brand-navy">Edit User Account</h3>
            <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="text-brand-secondary text-lg">&times;</button>
        </div>

        <form id="editUserForm" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Full Name</label>
                <input type="text" id="edit_u_name" name="name" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Email Address</label>
                <input type="email" id="edit_u_email" name="email" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">Role</label>
                    <select id="edit_u_role" name="role" required class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs font-semibold">
                        <option value="customer">Customer</option>
                        <option value="operator">Logistics Operator</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-brand-navy mb-1">New Password (optional)</label>
                    <input type="password" name="password" minlength="8" placeholder="Leave blank to keep" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Company / Organization</label>
                <input type="text" id="edit_u_company" name="company_name" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-brand-navy mb-1">Phone Number</label>
                <input type="text" id="edit_u_phone" name="phone" class="w-full px-3 py-2 bg-brand-bg border border-brand-border rounded-xl text-xs">
            </div>

            <div class="text-right pt-2 border-t border-brand-border space-x-2">
                <button type="button" onclick="document.getElementById('editUserModal').classList.add('hidden')" class="px-4 py-2 border border-brand-border rounded-xl text-xs font-semibold text-brand-secondary">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-brand-primary text-white rounded-xl text-xs font-semibold hover:bg-brand-primary-hover">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditUserModal(u) {
        document.getElementById('editUserForm').action = `/users/${u.id}`;
        document.getElementById('edit_u_name').value = u.name;
        document.getElementById('edit_u_email').value = u.email;
        document.getElementById('edit_u_role').value = u.role;
        document.getElementById('edit_u_company').value = u.company_name || '';
        document.getElementById('edit_u_phone').value = u.phone || '';
        document.getElementById('editUserModal').classList.remove('hidden');
    }
</script>
@endsection

