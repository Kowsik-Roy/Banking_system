<x-app-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold mb-4">Set Your 4-digit PIN</h2>

        <form method="POST" action="{{ route('user.savePin') }}">
            @csrf
            @error('pin')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
            <input type="password" name="pin" placeholder="Enter PIN"
                   class="border p-2 w-full mb-4" maxlength="4" required>

            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                CONFIRM
            </button>
        </form>
    </div>
</x-app-layout>
