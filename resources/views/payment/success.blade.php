@extends('layouts.app')

@section('content')
  <div class="flex items-center justify-center min-h-screen bg-gradient-to-br from-green-200 to-blue-100">
    <div class="flex flex-col items-center justify-center max-w-md w-full p-6 bg-white rounded-lg shadow-xl">
      <div class="text-center mb-8">
        <svg class="text-green-500 w-24 h-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M5 13l4 4L19 7"></path>
        </svg>
        <h2 class="text-3xl font-bold text-gray-800 mt-4">Payment Successful</h2>
      </div>

      <div class="text-center mb-6">
        <p class="text-gray-600 text-lg">Thank you for purchasing!</p>
      </div>

      <div class="text-center">
        <a href="{{ route('dashboard') }}"
          class="bg-blue-500 hover:bg-blue-700 text-white font-semibold rounded-md px-6 py-3 transition duration-300 ease-in-out transform hover:scale-105">Go to Dashboard</a>
      </div>
    </div>
  </div>
@endsection
