<template>
  <div class="light">
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md shadow-sm">
      <div class="flex justify-between items-center px-8 h-20 max-w-full">
        <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tighter">
          Indigo Horizon
        </div>
        <div class="hidden md:flex space-x-8 items-center">
          <a
            href="#"
            class="font-inter tracking-tight font-bold text-sm uppercase text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors hover:scale-105 transition-transform duration-200"
          >
            Trips
          </a>
          <a
            href="#"
            class="font-inter tracking-tight font-bold text-sm uppercase text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors hover:scale-105 transition-transform duration-200"
          >
            Explore
          </a>
          <a
            href="#"
            class="font-inter tracking-tight font-bold text-sm uppercase text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors hover:scale-105 transition-transform duration-200"
          >
            Planners
          </a>
          <a
            href="#"
            class="font-inter tracking-tight font-bold text-sm uppercase text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 transition-colors hover:scale-105 transition-transform duration-200"
          >
            Guides
          </a>
        </div>
        <div class="flex items-center space-x-6">
          <span class="material-symbols-outlined text-slate-500 cursor-pointer hover:scale-105 transition-all">
            notifications
          </span>
          <div
            class="flex items-center space-x-2 border-b-2 border-indigo-600 pb-1 text-indigo-600 cursor-pointer active:scale-95 transition-all"
          >
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1">
              account_circle
            </span>
            <span class="font-inter tracking-tight font-bold text-sm uppercase">Account</span>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content Area -->
    <main class="flex-grow pt-32 pb-20 px-4 md:px-8 max-w-7xl mx-auto w-full">
      <!-- Header Section -->
      <header class="mb-12">
        <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter mb-4">
          Invoice History
        </h1>
        <p class="text-slate-500 text-lg max-w-2xl">
          Manage your travel expenses, download receipts, and track the status of your upcoming and past
          adventure bookings.
        </p>
      </header>

      <!-- Filters and Search Module -->
      <section class="mb-10">
        <div
          class="bg-surface-bright p-6 rounded-xl shadow-2xl border border-outline flex flex-col lg:flex-row gap-6 items-end lg:items-center"
        >
          <div class="flex-grow w-full">
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">
              Search Records
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                search
              </span>
              <input
                v-model="filters.searchTerm"
                type="text"
                placeholder="Invoice ID or Tour Name..."
                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
              />
            </div>
          </div>
          <div class="w-full lg:w-48">
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Status</label>
            <select
              v-model="filters.status"
              class="w-full px-4 py-3 bg-slate-50 border border-outline rounded-lg focus:ring-2 focus:ring-primary outline-none appearance-none cursor-pointer"
            >
              <option value="">All Statuses</option>
              <option value="Paid">Paid</option>
              <option value="Pending">Pending</option>
              <option value="Refunded">Refunded</option>
            </select>
          </div>
          <div class="w-full lg:w-64">
            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">
              Date Range
            </label>
            <div class="relative">
              <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                calendar_today
              </span>
              <input
                v-model="filters.dateRange"
                type="date"
                class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-outline rounded-lg focus:ring-2 focus:ring-primary outline-none"
              />
            </div>
          </div>
          <button
            @click="applyFilters"
            class="w-full lg:w-auto px-8 py-3.5 bg-primary text-white font-bold rounded-lg hover:scale-105 active:scale-95 transition-all shadow-lg flex items-center justify-center gap-2"
          >
            <span class="material-symbols-outlined text-sm">filter_list</span>
            Apply Filters
          </button>
        </div>
      </section>

      <!-- Invoices List -->
      <section class="bg-surface-bright rounded-xl overflow-hidden shadow-sm border border-outline">
        <div v-if="filteredInvoices.length > 0" class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-outline">
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500">
                  Invoice ID
                </th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500">Date</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500">Tour Name</th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500 text-right">
                  Amount
                </th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500 text-center">
                  Status
                </th>
                <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-500 text-right">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline">
              <tr
                v-for="invoice in paginatedInvoices"
                :key="invoice.id"
                class="hover:bg-slate-50/50 transition-colors"
              >
                <td class="px-6 py-5 font-mono text-sm text-slate-600 font-medium">{{ invoice.invoiceId }}</td>
                <td class="px-6 py-5 text-sm text-slate-600">{{ invoice.date }}</td>
                <td class="px-6 py-5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                      <img
                        :src="invoice.image"
                        :alt="invoice.tourName"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <span class="font-bold text-slate-900">{{ invoice.tourName }}</span>
                  </div>
                </td>
                <td class="px-6 py-5 text-right font-bold text-slate-900">{{ invoice.amount }}</td>
                <td class="px-6 py-5 text-center">
                  <span :class="getStatusBadgeClass(invoice.status)">
                    {{ invoice.status }}
                  </span>
                </td>
                <td class="px-6 py-5 text-right">
                  <div class="flex justify-end gap-3">
                    <button
                      @click="downloadPDF(invoice.id)"
                      class="p-2 text-slate-400 hover:text-primary transition-colors hover:scale-110 active:scale-90"
                      title="Download PDF"
                    >
                      <span class="material-symbols-outlined">download</span>
                    </button>
                    <button
                      @click="viewDetails(invoice.id)"
                      class="p-2 text-slate-400 hover:text-primary transition-colors hover:scale-110 active:scale-90"
                      title="View Details"
                    >
                      <span class="material-symbols-outlined">visibility</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div
          v-else
          class="flex flex-col items-center justify-center py-20 px-6 text-center"
        >
          <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
          </div>
          <h3 class="text-xl font-bold text-slate-900 mb-2">No invoices found</h3>
          <p class="text-slate-500 max-w-xs mx-auto">
            Try adjusting your filters or search terms to find what you're looking for.
          </p>
          <button
            @click="clearFilters"
            class="mt-8 px-6 py-2 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-white transition-all"
          >
            Clear Filters
          </button>
        </div>
      </section>

      <!-- Pagination -->
      <div v-if="filteredInvoices.length > 0" class="mt-8 flex items-center justify-between">
        <span class="text-sm text-slate-500 font-medium">
          Showing
          <span class="text-slate-900">{{ (currentPage - 1) * itemsPerPage + 1 }}</span>
          to
          <span class="text-slate-900">{{
            Math.min(currentPage * itemsPerPage, filteredInvoices.length)
          }}</span>
          of
          <span class="text-slate-900">{{ filteredInvoices.length }}</span>
          entries
        </span>
        <div class="flex gap-2">
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            :class="[
              'w-10 h-10 rounded-lg border border-outline flex items-center justify-center transition-colors',
              currentPage === 1
                ? 'text-slate-400 cursor-not-allowed'
                : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined">chevron_left</span>
          </button>

          <button
            v-for="page in totalPages"
            :key="page"
            @click="currentPage = page"
            :class="[
              'w-10 h-10 rounded-lg font-bold transition-colors',
              currentPage === page
                ? 'bg-primary text-white'
                : 'border border-outline text-slate-600 hover:bg-slate-50'
            ]"
          >
            {{ page }}
          </button>

          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            :class="[
              'w-10 h-10 rounded-lg border border-outline flex items-center justify-center transition-colors',
              currentPage === totalPages
                ? 'text-slate-400 cursor-not-allowed'
                : 'text-slate-600 hover:bg-slate-50'
            ]"
          >
            <span class="material-symbols-outlined">chevron_right</span>
          </button>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer
      class="w-full py-12 px-8 mt-auto bg-slate-50 dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800"
    >
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:flex lg:justify-between items-center gap-6">
        <div class="text-lg font-bold text-slate-900 dark:text-white">Indigo Horizon</div>
        <div class="flex flex-wrap gap-x-8 gap-y-2">
          <a
            href="#"
            class="font-inter text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors inline-block"
          >
            Privacy Policy
          </a>
          <a
            href="#"
            class="font-inter text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors inline-block"
          >
            Terms of Service
          </a>
          <a
            href="#"
            class="font-inter text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors inline-block"
          >
            Support Center
          </a>
          <a
            href="#"
            class="font-inter text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors inline-block"
          >
            Trust & Safety
          </a>
          <a
            href="#"
            class="font-inter text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors inline-block"
          >
            Cookie Settings
          </a>
        </div>
        <div class="text-slate-500 text-sm font-inter font-medium">
          © 2024 Indigo Horizon Travel Group. All rights reserved.
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'UserLichSuHoaDon',
  data() {
    return {
      currentPage: 1,
      itemsPerPage: 4,
      filters: {
        searchTerm: '',
        status: '',
        dateRange: ''
      },
      allInvoices: [
        {
          id: 1,
          invoiceId: '#INV-882910',
          date: 'Oct 24, 2023',
          tourName: 'Yosemite Valley Expedition',
          amount: '$1,240.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDicSK3Y7lWI8I7hojLBrHbc1iP9NsDBtE5GGs_C9uvyssME0E9yuI3qRo23o3IEvKahRTlyfMv6_HEn9Idtv_QvcRf-S342aAa7qMtJWlR4U_uifPLeTaYjYNb0gN8F2lzyZZGKn4FBgRcGGxHsxBf56i5TUfIe5lBFnVt80uv2y552eq8xYHhgodQjTQG9Q4qBQB9oRw4wOCus4RqOxaaC-BEA_B9lQ5wPfL19fUUmex0FWYmyFS1fkCTse2IPfa7ighkLjkBhoU'
        },
        {
          id: 2,
          invoiceId: '#INV-882905',
          date: 'Nov 12, 2023',
          tourName: 'Kyoto Heritage Walk',
          amount: '$450.00',
          status: 'Pending',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBqL6eGsbPJ0cCeMfep1eyJVLGOfxwC5CBk2o5DOv3OHWj5Y7OXWPyBq-ObrhTwjsJTC5N7jLCNbvsYzLb7IPNq2eDn6bQSBNU6JCkbfCX8vn8tlXEQXUydP5KYWVHDHdsBJ8ixhmabjNYFXNzgeNxrFKKMLjKS15wTpWM7-UOGazcmh7WoADXX77wsao1EKN2auLmdPI03aKDVB4rx0uqw3zbJfwlakH4UphQUxTFzOnIvLTvocqwB1PQAgsf12tExFvhIoVjZp40'
        },
        {
          id: 3,
          invoiceId: '#INV-882898',
          date: 'Sep 05, 2023',
          tourName: 'Agra Sunrise Private Tour',
          amount: '$215.00',
          status: 'Refunded',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuD5Zuet-GY7VAakMvDvEbAESSp0_bAjF79I6A1qTdg2OPz7RauMXw8dSM2TAamsBhTaWdBXDjqMhKzMaRKmeZqT-7peUxk783LJMOYDi3FfE3UiXXehdbIiWl2mVncBumRR4NjUpU3OUn8KhQGkuRwx205kBqLnqUZMlweG3zSmh4kFea-12N6rTL2y8SPnQxUKKQtvA0J8wrKN_eS1OFFl7M8bpZEGBJ6atxmjv3mpUcm6Ikjfqhc9qUJFDBL1XnMQ6YywDvFNFqo'
        },
        {
          id: 4,
          invoiceId: '#INV-882741',
          date: 'Aug 20, 2023',
          tourName: 'Moscow Winter Wonders',
          amount: '$2,890.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuANEypp70QJwJyY0jFAWMtv1Vwb9EX6QQnNLS9KZigAXV-1nA8WkQIckrqHgcrXvhHWysFnGs3ofOgO8l79AWNDkK2-eIdaNBq_yK3qRKMRpwtdFSHfdM881hHVEOHipc8OyHqSjTjF5BMbHyP4d0sh-xaId5DYQljOivsO9coHRUHSgFhB0RJkqEpJ1bTW2IYY2T7NwigUJta0oaYvWix4LDn5gktK0Dj4L5gPwMFP9KTGmtEov--ibRiL69y2jheiphAQH5m0HVc'
        },
        {
          id: 5,
          invoiceId: '#INV-882630',
          date: 'Jul 10, 2023',
          tourName: 'Paris City Romance',
          amount: '$1,850.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuANEypp70QJwJyY0jFAWMtv1Vwb9EX6QQnNLS9KZigAXV-1nA8WkQIckrqHgcrXvhHWysFnGs3ofOgO8l79AWNDkK2-eIdaNBq_yK3qRKMRpwtdFSHfdM881hHVEOHipc8OyHqSjTjF5BMbHyP4d0sh-xaId5DYQljOivsO9coHRUHSgFhB0RJkqEpJ1bTW2IYY2T7NwigUJta0oaYvWix4LDn5gktK0Dj4L5gPwMFP9KTGmtEov--ibRiL69y2jheiphAQH5m0HVc'
        },
        {
          id: 6,
          invoiceId: '#INV-882512',
          date: 'Jun 03, 2023',
          tourName: 'Bali Beach Resort',
          amount: '$780.00',
          status: 'Pending',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBqL6eGsbPJ0cCeMfep1eyJVLGOfxwC5CBk2o5DOv3OHWj5Y7OXWPyBq-ObrhTwjsJTC5N7jLCNbvsYzLb7IPNq2eDn6bQSBNU6JCkbfCX8vn8tlXEQXUydP5KYWVHDHdsBJ8ixhmabjNYFXNzgeNxrFKKMLjKS15wTpWM7-UOGazcmh7WoADXX77wsao1EKN2auLmdPI03aKDVB4rx0uqw3zbJfwlakH4UphQUxTFzOnIvLTvocqwB1PQAgsf12tExFvhIoVjZp40'
        },
        {
          id: 7,
          invoiceId: '#INV-882401',
          date: 'May 20, 2023',
          tourName: 'Dubai Desert Safari',
          amount: '$620.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDicSK3Y7lWI8I7hojLBrHbc1iP9NsDBtE5GGs_C9uvyssME0E9yuI3qRo23o3IEvKahRTlyfMv6_HEn9Idtv_QvcRf-S342aAa7qMtJWlR4U_uifPLeTaYjYNb0gN8F2lzyZZGKn4FBgRcGGxHsxBf56i5TUfIe5lBFnVt80uv2y552eq8xYHhgodQjTQG9Q4qBQB9oRw4wOCus4RqOxaaC-BEA_B9lQ5wPfL19fUUmex0FWYmyFS1fkCTse2IPfa7ighkLjkBhoU'
        },
        {
          id: 8,
          invoiceId: '#INV-882290',
          date: 'Apr 15, 2023',
          tourName: 'Bangkok Night Tour',
          amount: '$345.00',
          status: 'Refunded',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuD5Zuet-GY7VAakMvDvEbAESSp0_bAjF79I6A1qTdg2OPz7RauMXw8dSM2TAamsBhTaWdBXDjqMhKzMaRKmeZqT-7peUxk783LJMOYDi3FfE3UiXXehdbIiWl2mVncBumRR4NjUpU3OUn8KhQGkuRwx205kBqLnqUZMlweG3zSmh4kFea-12N6rTL2y8SPnQxUKKQtvA0J8wrKN_eS1OFFl7M8bpZEGBJ6atxmjv3mpUcm6Ikjfqhc9qUJFDBL1XnMQ6YywDvFNFqo'
        },
        {
          id: 9,
          invoiceId: '#INV-882145',
          date: 'Mar 08, 2023',
          tourName: 'Swiss Alps Adventure',
          amount: '$3,200.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuANEypp70QJwJyY0jFAWMtv1Vwb9EX6QQnNLS9KZigAXV-1nA8WkQIckrqHgcrXvhHWysFnGs3ofOgO8l79AWNDkK2-eIdaNBq_yK3qRKMRpwtdFSHfdM881hHVEOHipc8OyHqSjTjF5BMbHyP4d0sh-xaId5DYQljOivsO9coHRUHSgFhB0RJkqEpJ1bTW2IYY2T7NwigUJta0oaYvWix4LDn5gktK0Dj4L5gPwMFP9KTGmtEov--ibRiL69y2jheiphAQH5m0HVc'
        },
        {
          id: 10,
          invoiceId: '#INV-882001',
          date: 'Feb 14, 2023',
          tourName: 'Great Wall Trek',
          amount: '$1,500.00',
          status: 'Pending',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBqL6eGsbPJ0cCeMfep1eyJVLGOfxwC5CBk2o5DOv3OHWj5Y7OXWPyBq-ObrhTwjsJTC5N7jLCNbvsYzLb7IPNq2eDn6bQSBNU6JCkbfCX8vn8tlXEQXUydP5KYWVHDHdsBJ8ixhmabjNYFXNzgeNxrFKKMLjKS15wTpWM7-UOGazcmh7WoADXX77wsao1EKN2auLmdPI03aKDVB4rx0uqw3zbJfwlakH4UphQUxTFzOnIvLTvocqwB1PQAgsf12tExFvhIoVjZp40'
        },
        {
          id: 11,
          invoiceId: '#INV-881890',
          date: 'Jan 22, 2023',
          tourName: 'Iceland Glacier Tour',
          amount: '$2,100.00',
          status: 'Paid',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuDicSK3Y7lWI8I7hojLBrHbc1iP9NsDBtE5GGs_C9uvyssME0E9yuI3qRo23o3IEvKahRTlyfMv6_HEn9Idtv_QvcRf-S342aAa7qMtJWlR4U_uifPLeTaYjYNb0gN8F2lzyZZGKn4FBgRcGGxHsxBf56i5TUfIe5lBFnVt80uv2y552eq8xYHhgodQjTQG9Q4qBQB9oRw4wOCus4RqOxaaC-BEA_B9lQ5wPfL19fUUmex0FWYmyFS1fkCTse2IPfa7ighkLjkBhoU'
        },
        {
          id: 12,
          invoiceId: '#INV-881745',
          date: 'Dec 10, 2022',
          tourName: 'Machu Picchu Expedition',
          amount: '$1,980.00',
          status: 'Refunded',
          image:
            'https://lh3.googleusercontent.com/aida-public/AB6AXuD5Zuet-GY7VAakMvDvEbAESSp0_bAjF79I6A1qTdg2OPz7RauMXw8dSM2TAamsBhTaWdBXDjqMhKzMaRKmeZqT-7peUxk783LJMOYDi3FfE3UiXXehdbIiWl2mVncBumRR4NjUpU3OUn8KhQGkuRwx205kBqLnqUZMlweG3zSmh4kFea-12N6rTL2y8SPnQxUKKQtvA0J8wrKN_eS1OFFl7M8bpZEGBJ6atxmjv3mpUcm6Ikjfqhc9qUJFDBL1XnMQ6YywDvFNFqo'
        }
      ]
    }
  },
  computed: {
    filteredInvoices() {
      return this.allInvoices.filter((invoice) => {
        let matches = true

        // Search filter
        if (this.filters.searchTerm) {
          const searchLower = this.filters.searchTerm.toLowerCase()
          matches =
            matches &&
            (invoice.invoiceId.toLowerCase().includes(searchLower) ||
              invoice.tourName.toLowerCase().includes(searchLower))
        }

        // Status filter
        if (this.filters.status) {
          matches = matches && invoice.status === this.filters.status
        }

        // Date filter
        if (this.filters.dateRange) {
          matches = matches && invoice.date.includes(this.filters.dateRange)
        }

        return matches
      })
    },
    totalPages() {
      return Math.ceil(this.filteredInvoices.length / this.itemsPerPage)
    },
    paginatedInvoices() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredInvoices.slice(start, end)
    }
  },
  methods: {
    getStatusBadgeClass(status) {
      const baseClass = 'px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-full border'

      switch (status) {
        case 'Paid':
          return `${baseClass} bg-green-100 text-green-700 border-green-200`
        case 'Pending':
          return `${baseClass} bg-amber-100 text-amber-700 border-amber-200`
        case 'Refunded':
          return `${baseClass} bg-slate-100 text-slate-500 border-slate-200`
        default:
          return `${baseClass} bg-slate-100 text-slate-500 border-slate-200`
      }
    },
    applyFilters() {
      this.currentPage = 1 // Reset to first page when filters are applied
    },
    clearFilters() {
      this.filters = {
        searchTerm: '',
        status: '',
        dateRange: ''
      }
      this.currentPage = 1
    },
    downloadPDF(invoiceId) {
      console.log(`Downloading PDF for invoice ${invoiceId}`)
      // Add your PDF download logic here
    },
    viewDetails(invoiceId) {
      console.log(`Viewing details for invoice ${invoiceId}`)
      // Add your view details logic here - could navigate to detail page
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++
      }
    },
    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--
      }
    }
  }
}
</script>

<style scoped>
body {
  font-family: 'Inter', sans-serif;
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
</style>
