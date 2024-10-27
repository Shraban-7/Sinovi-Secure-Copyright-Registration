<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sinovi - Secure Copyright Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="bg-blue-600 p-4">
    <div class="container mx-auto flex justify-between items-center">
      <a href="{{ route('home') }}" class="text-white text-2xl font-bold">Sinovi</a>



      <div class="space-x-4">
        @auth
            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">Dashboard</a>
        @endauth
        @guest
            <a href="{{ route('login') }}" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">Login</a>
            <a href="{{ route('register') }}" class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-600">Register</a>
        @endguest
      </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden flex-col space-y-2 mt-2">

      <div class="flex space-x-4 px-4">
        <a href="#login" class="px-4 py-2 text-white bg-green-500 rounded hover:bg-green-600">Login</a>
        <a href="#register" class="px-4 py-2 text-white bg-indigo-500 rounded hover:bg-indigo-600">Register</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="min-h-screen flex items-center justify-center">
    <div class="max-w-3xl w-full px-6 py-12 bg-white rounded-lg shadow-lg">
      <h1 class="text-4xl font-bold text-center text-blue-600 mb-4">
        Sinovi: Secure Copyright Registration
      </h1>
      <p class="text-gray-700 text-center mb-8">
        Protecting National ID/ Passport Images with AES-256 Encryption.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="p-6 border rounded-lg hover:shadow-md transition">
          <h2 class="text-xl font-semibold text-gray-800">Secure Storage</h2>
          <p class="mt-2 text-gray-600">
            Ensure that all sensitive data remains encrypted and protected.
          </p>
        </div>

        <div class="p-6 border rounded-lg hover:shadow-md transition">
          <h2 class="text-xl font-semibold text-gray-800">Waterfall Development</h2>
          <p class="mt-2 text-gray-600">
            Following a structured Waterfall methodology to deliver the project efficiently.
          </p>
        </div>

        <div class="p-6 border rounded-lg hover:shadow-md transition">
          <h2 class="text-xl font-semibold text-gray-800">AES-256 Encryption</h2>
          <p class="mt-2 text-gray-600">
            Your data is encrypted with the most secure standard available.
          </p>
        </div>

        <div class="p-6 border rounded-lg hover:shadow-md transition">
          <h2 class="text-xl font-semibold text-gray-800">User-Friendly Platform</h2>
          <p class="mt-2 text-gray-600">
            Accessible to both students and teachers for copyright registration.
          </p>
        </div>
      </div>


    </div>
  </div>



</body>

</html>
