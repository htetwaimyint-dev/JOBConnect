<template>
  <!-- <div class="min-h-screen bg-gray-50 p-8 font-sans"> -->
  <!-- Main Card -->
  <div
    class="max-w-5xl mx-auto bg-white rounded-xl shadow-sm border border-gray-200"
  >
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 flex justify-between items-start">
      <div>
        <h2 class="text-2xl font-semibold text-gray-800">
          Category Management
        </h2>
        <p class="text-gray-500 text-sm mt-1">Manage your job categories</p>
      </div>
      <span
        class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full"
      >
        Admin
      </span>
    </div>

    <!-- Toolbar -->
    <div class="p-6 pb-2">
      <div class="flex justify-between items-center">
        <!-- Search Bar -->
        <div class="relative w-full max-w-md">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3">
            <svg
              class="w-5 h-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              ></path>
            </svg>
          </span>
          <input
            type="text"
            placeholder="Search categories..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
          />
        </div>

        <!-- Add Button -->
        <button
          class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors"
        >
          <svg
            class="w-4 h-4 mr-2"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4v16m8-8H4"
            ></path>
          </svg>
          Add Category
        </button>
      </div>
    </div>

    <!-- Selection Status & Delete Action -->
    <div class="px-6 py-2 flex justify-between items-center">
      <span class="text-sm text-gray-500">3 categories selected</span>
      <button
        class="text-red-500 bg-red-50 hover:bg-red-100 font-medium py-1.5 px-3 rounded-md text-sm transition-colors"
      >
        Delete Selected
      </button>
    </div>

    <!-- Table -->
    <div class="px-6 pb-6 mt-2">
      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-gray-50 border-b border-gray-200 text-gray-700 text-sm"
            >
              <th></th>
              <th class="py-3 px-4 font-medium">No.</th>
              <th class="py-3 px-4 font-medium">Logo</th>
              <th class="py-3 px-4 font-medium">Name</th>
              <th class="py-3 px-4 font-medium">Option</th>
            </tr>
          </thead>
          <tbody>
            <!-- Row 1 -->
            <tr
              class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <td class="py-4 px-4">
                <input
                  type="checkbox"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
              </td>
              <td class="py-4 px-4 text-gray-600">01</td>
              <td class="py-4 px-4 text-xl">💻</td>
              <td class="py-4 px-4 font-medium text-gray-800">
                Information Technology
              </td>
              <td class="py-4 px-4">
                <div class="flex space-x-3 text-lg">
                  <button class="text-blue-500 hover:text-blue-700">👁️</button>
                  <button class="text-orange-400 hover:text-orange-600">
                    ✏️
                  </button>
                  <button class="text-red-500 hover:text-red-700">🗑️</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination Footer -->
    <div
      class="px-6 py-4 border-t border-gray-200 flex justify-between items-center bg-gray-50 rounded-b-xl"
    >
      <span class="text-sm text-gray-500">Showing 1–3 of 10</span>

      <div class="flex items-center space-x-2">
        <button
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50 disabled:opacity-50"
        >
          Previous
        </button>
        <button
          class="px-3 py-1 text-sm border border-blue-500 rounded-md bg-blue-50 text-blue-600 font-medium"
        >
          1
        </button>
        <button
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50"
        >
          2
        </button>
        <button
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50"
        >
          3
        </button>
        <button
          class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white text-gray-600 hover:bg-gray-50"
        >
          Next
        </button>
      </div>
    </div>
  </div>
  <!-- </div> -->
</template>

<script setup>
// Logic (JavaScript) ကို ဒီနေရာမှာ ရေးပါမယ်။
// ဥပမာ - Search လုပ်တဲ့အခါ သို့မဟုတ် Checkbox နှိပ်တဲ့အခါ အလုပ်လုပ်ဖို့ ရေးနိုင်ပါတယ်။
</script>
