<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (auth()->user()->isAdmin())
                        <div class="container mx-auto p-4">
                            <h1 class="text-2xl font-bold mb-4">All User</h1>

                            <table
                                class="table-auto w-full border-collapse border border-gray-300 shadow-lg rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Name
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Nid Verified
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-gray-50 transition duration-300 ease-in-out">
                                            <td class="px-6 py-4">
                                                {{ $user->name }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">{{ $user->email }}</td>
                                            <td class="px-6 py-4 text-gray-700">
                                                @php
                                                    $verified = \App\Models\NidVerification::where(
                                                        'email',
                                                        $user->email,
                                                    )->first();
                                                @endphp
                                                {{ $verified ? 'Yes' : 'NO' }}

                                            </td>

                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $user->created_at->format('d M, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="container mx-auto p-4">
                            <h1 class="text-2xl font-bold mb-4">NID Verification</h1>

                            @if (session('success'))
                                <div id="success-alert"
                                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                                    role="alert">
                                    <span>{{ session('success') }}</span>
                                    <button type="button" class="absolute top-0 right-0 px-4 py-3"
                                        onclick="removeAlert('success-alert')">
                                        <span class="text-green-500 font-bold">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div id="error-alert"
                                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                                    role="alert">
                                    <span>{{ session('error') }}</span>
                                    <button type="button" class="absolute top-0 right-0 px-4 py-3"
                                        onclick="removeAlert('error-alert')">
                                        <span class="text-red-500 font-bold">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    setTimeout(() => {
                                        const successAlert = document.getElementById('success-alert');
                                        const errorAlert = document.getElementById('error-alert');
                                        if (successAlert) fadeOutAndRemove(successAlert);
                                        if (errorAlert) fadeOutAndRemove(errorAlert);
                                    }, 3000);

                                    window.removeAlert = function(id) {
                                        const alert = document.getElementById(id);
                                        if (alert) fadeOutAndRemove(alert);
                                    };

                                    function fadeOutAndRemove(element) {
                                        element.style.transition = 'opacity 0.5s ease';
                                        element.style.opacity = '0';
                                        setTimeout(() => element.remove(), 500);
                                    }
                                });
                            </script>

                            <form action="{{ route('nid.verify') }}" method="POST" enctype="multipart/form-data"
                                class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nid_image">
                                        NID Card Image
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="nid_image" type="file" name="nid_image" required>
                                </div>
                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="nid_number">
                                        NID Number
                                    </label>
                                    <input
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        id="nid_number" type="text" name="nid_number" required>
                                </div>
                                <div class="flex items-center justify-between">
                                    <button
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                        type="submit">
                                        Verify NID
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="container mx-auto p-4">
                            <h1 class="text-2xl font-bold mb-4">All NID Verifications</h1>

                            <table
                                class="table-auto w-full border-collapse border border-gray-300 shadow-lg rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            NID Image
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-gray-600 font-semibold uppercase tracking-wider border-b border-gray-300">
                                            Date
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach ($user_nids as $nid_verification)
                                        <tr class="hover:bg-gray-50 transition duration-300 ease-in-out">
                                            <td class="px-6 py-4">
                                                <div class="overflow-hidden rounded-md shadow-md h-48 w-96">
                                                    <button
                                                        onclick="showModal('{{ asset($nid_verification->censored_image_path) }}')">
                                                        <img src="{{ asset($nid_verification->censored_image_path) }}"
                                                            alt="Censored Image" class="object-cover">
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-700">{{ $nid_verification->email }}</td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $nid_verification->created_at->format('d M, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-auto max-h-[80vh] w-auto max-w-full">
                            <img id="modal-image" src="" alt="Censored Image" class="object-contain">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="hideModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showModal(imageSrc) {
            document.getElementById('modal-image').src = imageSrc;
            document.getElementById('modal').classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
