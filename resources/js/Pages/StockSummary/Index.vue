<style>
/* General DataTables Pagination Container Style */
.dataTables_wrapper .dataTables_paginate {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
}

/* Style the filter container */
#TransitionTable_filter {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-bottom: 16px; /* Add spacing below the filter */
}

/* Style the label and input field inside the filter */
#TransitionTable_filter label {
  font-size: 17px;
  color: #000000; /* Match text color of the table header */
  display: flex;
  align-items: center;
}

/* Style the input field */
#TransitionTable_filter input[type="search"] {
  font-weight: 400;
  padding: 9px 15px;
  font-size: 14px;
  color: #000000cc;
  border: 1px solid rgb(209 213 219);
  border-radius: 5px;
  background: #fff;
  outline: none;
  transition: all 0.5s ease;
}
#TransitionTable_filter input[type="search"]:focus {
  outline: none; /* Removes the default outline */
  border: 1px solid #4b5563;
  box-shadow: none; /* Removes any focus box-shadow */
}

#TransitionTable_filter {
  float: left;
}

.dataTables_wrapper {
  margin-bottom: 10px;
}
</style>

<template>
  <Head title="Stock Summary" />
  <Banner />
  <div
    class="flex flex-col items-center justify-start min-h-screen py-8 space-y-8 bg-gray-100 px-36"
  >
    <Header />
    <div class="w-5/6 py-12 space-y-24">
      <div class="flex items-center justify-between float-end">
        <p class="text-3xl italic font-bold text-black">
          <span class="px-4 py-1 mr-3 text-white bg-black rounded-xl">{{
            totalStockTransactions
          }}</span>
          <span class="text-xl">/ Stock Summary</span>
        </p>
      </div>

      <div class="flex w-full">
        <div class="flex items-center w-full h-16 space-x-4 rounded-2xl">
          <Link href="/">
            <img src="/images/back-arrow.png" class="w-14 h-14" />
          </Link>
          <p class="text-4xl font-bold tracking-wide text-black uppercase">
            Stock Summary
          </p>
        </div>
        <div class="flex justify-end w-full"></div>
      </div>
      <div class="flex justify-between w-full mb-4">
  <!-- Date Picker -->
  <div class="flex items-center space-x-4">
    <label for="datePicker" class="text-lg font-semibold text-gray-700">Filter by Date:</label>
    <input
      id="datePicker"
      type="date"
      v-model="selectedDate"
      class="p-2 border border-gray-300 rounded-md shadow-sm"
      @change="filterByDate"
    />
  </div>
</div>
      <template v-if="allStockTransactions && allStockTransactions.length > 0">
        <div class="overflow-x-auto">
          <table
            id="TransitionTable"
            class="w-full text-gray-700 bg-white border border-gray-300 rounded-lg shadow-md table-auto"
          >
            <thead>
              <tr
                class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 text-[12px] text-white border-b border-blue-700"
              >
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  #
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Product Name
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Received
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Daily Franchise
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Retail
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Courier Transfer
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Pending
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Meeting Order
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Staff Issued
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Director Issued
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Return
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Promotion
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Sample
                </th>

                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Damaged
                </th>

                <!-- <th class="p-4 font-semibold tracking-wide text-left uppercase">
                    Transaction Date
                 </th> -->
                <th class="p-4 font-semibold tracking-wide text-left uppercase">
                  Total Remaining
                </th>
                <th class="p-4 font-semibold tracking-wide text-left uppercase hidden">
                  Date
                </th>
     
              </tr>
            </thead>
            <tbody class="text-[13px] font-normal">
              <tr
                v-for="(stock, index) in allStockTransactions"
                :key="stock.id"
                class="transition duration-200 ease-in-out hover:bg-gray-200 hover:shadow-lg"
              >{{ console.log('Stock:', stock) }}
                <td class="px-6 py-3 text- first-letter:">{{ index + 1 }}</td>
                <td class="p-4 font-bold border-gray-200">
                  {{ stock.product_name || "N/A" }}
                </td>
         
                <td class="p-4 font-bold border-gray-200">
                  {{stock.received !== null && stock.received !== undefined ? stock.received : "N/A"}}
                </td>
                <td class="p-4 font-bold border-gray-200">
                  {{stock.daily_franchise !== null && stock.daily_franchise !== undefined ? stock.daily_franchise : "N/A"}}

                </td>
         
                <td class="p-4 font-bold border-gray-200">
                
                  {{stock.retail !== null && stock.retail !== undefined ? stock.retail : "N/A"}}
                </td>
                <td class="p-4 font-bold border-gray-200">
                  {{stock.courier_transfer !== null && stock.courier_transfer !== undefined ? stock.courier_transfer : "N/A"}}

                </td>
         
                <td class="p-4 font-bold border-gray-200">
                  {{stock.pending !== null && stock.pending !== undefined ? stock.pending : "N/A"}}

                </td>

                <td class="p-4 font-bold border-gray-200">
                  {{stock.meeting_order !== null && stock.meeting_order !== undefined ? stock.meeting_order : "N/A"}}

           
                </td>
                <td class="p-4 font-bold border-gray-200">
                  {{stock.staff_issued !== null && stock.staff_issued !== undefined ? stock.staff_issued : "N/A"}}


                </td>
         
                <td class="p-4 font-bold border-gray-200">
                  {{stock.director_issued !== null && stock.director_issued !== undefined ? stock.director_issued : "N/A"}}

                </td>


                <td class="p-4 font-bold border-gray-200">
                  {{stock.return !== null && stock.return !== undefined ? stock.return : "N/A"}}

                </td>
                <td class="p-4 font-bold border-gray-200">
                  {{stock.promotion !== null && stock.promotion !== undefined ? stock.promotion : "N/A"}}

                </td>
         
                <td class="p-4 font-bold border-gray-200">
                  {{stock.sample !== null && stock.sample !== undefined ? stock.sample : "N/A"}}

                </td>


                <td class="p-4 border-gray-200">
                  {{stock.damaged !== null && stock.damaged !== undefined ? stock.damaged : "N/A"}}

                </td>
                <td class="p-4 font-bold border-gray-200">
                  {{stock.total_stocks_remaining !== null && stock.total_stocks_remaining !== undefined ? stock.total_stocks_remaining : "N/A"}}

                </td>
                <td class="p-4 font-bold border-gray-200 hidden">
                  {{stock.created_at !== null && stock.created_at !== undefined ? stock.created_at : "N/A"}}

                </td>

   
              </tr>
            </tbody>
          </table>
        </div>
      </template>
      <template v-else>
        <div class="col-span-4 text-center text-blue-500">
          <p class="text-center text-red-500 text-[17px]">
            No Summary Available
          </p>
        </div>
      </template>
    </div>
  </div>

  <StockUpdateModel
    :stocks="allStockTransactions"
    :selected-stock="selectedStock"
    v-model:open="isEditModalOpen"
  />
  <Footer />
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import Header from "@/Components/custom/Header.vue";
import Footer from "@/Components/custom/Footer.vue";
import StockUpdateModel from "@/Components/custom/StockTransResonModel.vue";
import Banner from "@/Components/Banner.vue";
import { HasRole } from "@/Utils/Permissions";

defineProps({
  allStockTransactions: Array,
  totalStockTransactions: Number,
});
const form = useForm({});

const isEditModalOpen = ref(false);
const selectedStock = ref(null);

const openEditModal = (stock) => {
  selectedStock.value = stock;
  isEditModalOpen.value = true;
};

const selectedDate = ref(null);

const filterByDate = () => {
  console.log('filter working');
  // Send the selected date to the backend
//  Inertia.get(route('stocks.display'), { date: selectedDate.value });
};

$(document).ready(function () {
  let table = $("#TransitionTable").DataTable({
    dom: "Bfrtip",
    pageLength: 10,
    buttons: [],
    columnDefs: [
      {
        targets: 2,
        searchable: false,
        orderable: false,
      },
    ],
    initComplete: function () {
      let searchInput = $("div.dataTables_filter input");
      searchInput.attr("placeholder", "Search ...");
      searchInput.on("keypress", function (e) {
        if (e.which == 13) {
          table.search(this.value).draw();
        }
      });

      $("#datePicker").on("change", function () {
        const selectedDate = $(this).val();
        if (selectedDate) {
          table.column(15).search(selectedDate).draw();
        } else {
          // If no date is selected, filter by today's date
          const today = new Date().toISOString().split("T")[0];
          table.column(15).search(today).draw();
        }
      });
    },
    language: {
      search: "",
    },
  });
});
</script>