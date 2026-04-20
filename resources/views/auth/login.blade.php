<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-700 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-md bg-white p-10 rounded-[2rem] shadow-2xl border border-white/50">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-black text-gray-900 mb-2">Selamat Datang</h2>
            <p class="text-gray-500 text-sm font-medium">Silakan masuk ke akun Anda untuk memulai absensi</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold border border-red-100">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-gray-300" placeholder="email" required autofocus>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                <input type="password" name="password" class="w-full p-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-gray-300" placeholder="password" required>
            </div>
            
            <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-bold rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 transition transform active:scale-95 text-lg">
                Login
            </button>
        </form>
    </div>

</body>
</html>