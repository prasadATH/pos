<template>
  <Head title="POS" />
  <Banner />
  <div
    class="flex flex-col items-center justify-start min-h-screen py-8 space-y-4 bg-gray-100 px-36"
  >
    <!-- Include the Header -->
    <Header />

    <div class="w-5/6 py-12 space-y-16">
      <div class="flex items-center justify-between space-x-4">
        <div class="flex w-full space-x-4">
          <Link href="/transactionHistory">
            <img src="/images/back-arrow.png" class="w-14 h-14" />
          </Link>
          <p class="pt-3 text-4xl font-bold tracking-wide text-black uppercase">
            Order History / Order - #{{ sale?.order_id }}
          </p>
        </div>
        <!-- <div class="flex items-center justify-between w-full space-x-4">
          <p class="text-3xl font-bold tracking-wide text-black">
            Order ID : #{{ sale?.order_id }}
          </p>
          <p class="text-3xl text-black cursor-pointer">
            <i @click="refreshData" class="ri-restart-line"></i>
          </p>
        </div> -->
      </div>
      <div class="flex w-full gap-4">
        <div class="flex w-full p-8 border-4 border-black rounded-3xl">
          <div class="flex flex-col items-start justify-center w-full px-12">
            <div class="flex items-center justify-between w-full">
              <h2 class="text-5xl font-bold text-black">Billing Details</h2>
              <!-- <span
                class="flex cursor-pointer"
                @click="isSelectModalOpen = true"
              >
                <p class="text-xl text-blue-600 font-bold">User Manual</p>
                <img src="/images/selectpsoduct.svg" class="w-6 h-6 ml-2" />
              </span> -->
            </div>

            <div
              class="flex items-end justify-between w-full my-5 rounded-2xl text-2xl"
            >
              <p>Type - {{ sale?.type }}</p>
            </div>

            <div class="w-full text-center">
              <p v-if="products.length === 0" class="text-2xl text-red-500">
                No Products to show
              </p>
            </div>

            <div
              class="flex items-center w-full py-4 border-b border-black"
              v-for="item in products"
              :key="item.id"
            >
              <!-- Checkbox Section -->
              <!-- <p>{{ item.status }}</p> -->
              <div class="flex items-center w-1/12">
                <input
                  type="checkbox"
                  v-model="item.selected"
                  :disabled="item.status === 'sold'"
                  :class="
                    item.status === 'sold'
                      ? 'cursor-not-allowed'
                      : 'cursor-pointer'
                  "
                  class="w-5 h-5 text-black border-2 border-black rounded"
                />
              </div>

              <!-- Image Section -->
              <div class="flex w-1/6">
                <img
                  :src="
                    item.image ? `/${item.image}` : '/images/placeholder.jpg'
                  "
                  alt="Supplier Image"
                  class="object-cover w-16 h-16 border border-gray-500"
                />
              </div>

              <!-- Content Section -->
              <div class="flex flex-col justify-between w-5/6">
                <p class="text-xl text-black">
                  {{ item.name }}
                </p>
                <div class="flex items-center justify-between w-full">
                  <div class="flex space-x-4">
                    <span
                      class="bg-[#D9D9D9] border-2 border-black h-8 w-12 text-black flex justify-center items-center rounded text-center"
                    >
                      {{ item.quantity }}
                    </span>
                  </div>
                  <div class="flex items-center justify-center">
                    <div>
                      <p
                        @click="applyDiscount(item.id)"
                        v-if="
                          item.discount &&
                          item.discount > 0 &&
                          item.apply_discount == false &&
                          !appliedCoupon
                        "
                        class="cursor-pointer py-1 text-center px-4 bg-green-600 rounded-xl font-bold text-white tracking-wider"
                      >
                        Apply {{ item.discount }}% off
                      </p>

                      <p
                        v-if="
                          item.discount &&
                          item.discount > 0 &&
                          item.apply_discount == true &&
                          !appliedCoupon
                        "
                        @click="removeDiscount(item.id)"
                        class="cursor-pointer py-1 text-center px-4 bg-red-600 rounded-xl font-bold text-white tracking-wider"
                      >
                        Remove {{ item.discount }}% Off
                      </p>
                      <p class="text-2xl font-bold text-black text-right">
                        {{ item.selling_price }}
                        LKR
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="w-full pt-6 space-y-2">
              <div class="flex items-center justify-between w-full px-16">
                <p class="text-xl">Sub Total</p>
                <p class="text-xl">{{ subtotal }} LKR</p>
              </div>
              <div
                class="flex items-center justify-between w-full px-16 py-2 pb-4 border-b border-black"
              >
                <p class="text-xl">Discount</p>
                <p class="text-xl">( {{ discount }} LKR )</p>
              </div>
              <div
                class="flex items-center justify-between w-full px-16 pt-4 pb-4 border-b border-black"
              >
                <p class="text-xl text-black">Custom Discount</p>
                <span>
                  <p>{{ custom_discount }} LKR</p>
                  <!-- <CurrencyInput
                    v-model="custom_discount"
                    :options="{ currency: 'EUR' }"
                  /> -->
                  <!-- <span class="ml-2">LKR</span> -->
                </span>
              </div>
              <div
                class="flex items-center justify-between w-full px-16 pt-4 pb-4 border-b border-black"
              >
                <p class="text-xl text-black">Cash</p>
                <span>
                  <p>{{ cash }} LKR</p>
                  <!-- <CurrencyInput
                    v-model="cash"
                    :options="{ currency: 'EUR' }"
                  /> -->
                  <!-- <span class="ml-2">LKR</span> -->
                </span>
              </div>
              <div class="flex items-center justify-between w-full px-16 pt-4">
                <p class="text-3xl text-black">Total</p>
                <p class="text-3xl text-black">{{ total }} LKR</p>
              </div>

              <div
                class="flex items-center justify-between w-full px-16 pt-4 pb-4"
              >
                <p class="text-xl text-black">Balance</p>
                <p>{{ balance }} LKR</p>
              </div>
            </div>

            <div class="flex flex-col w-full space-y-8">
              <div class="flex items-center justify-center w-full">
                <button
                  @click="
                    () => {
                      submitOrder();
                    }
                  "
                  type="button"
                  :disabled="products.length === 0"
                  :class="[
                    'w-full bg-black py-4 text-2xl font-bold tracking-wider text-center text-white uppercase rounded-xl',
                    products.length === 0
                      ? ' cursor-not-allowed'
                      : ' cursor-pointer',
                  ]"
                >
                  <i class="pr-4 ri-add-circle-fill"></i> Confirm Order
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <PosSuccessModel
    :open="isSuccessModalOpen"
    @update:open="handleModalOpenUpdate"
    :products="products"
    :cashier="loggedInUser"
    :customer="customer"
    :orderId="orderId"
    :cash="cash"
    :balance="balance"
    :subTotal="subtotal"
    :totalDiscount="totalDiscount"
    :total="total"
    :custom_discount="custom_discount"
  />
  <AlertModel v-model:open="isAlertModalOpen" :message="message" />

  <SelectProductModel
    v-model:open="isSelectModalOpen"
    :allcategories="allcategories"
    :colors="colors"
    :sizes="sizes"
    @selected-products="handleSelectedProducts"
  />
  <Footer />
</template>
<script setup>
import Header from "@/Components/custom/Header.vue";
import Footer from "@/Components/custom/Footer.vue";
import Banner from "@/Components/Banner.vue";
import PosSuccessModel from "@/Components/custom/PosSuccessModel.vue";
import AlertModel from "@/Components/custom/AlertModel.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, onMounted, computed, watch } from "vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import CurrencyInput from "@/Components/custom/CurrencyInput.vue";
import SelectProductModel from "@/Components/custom/SelectProductModel.vue";
import ProductAutoComplete from "@/Components/custom/ProductAutoComplete.vue";

const product = ref(null);
const error = ref(null);
const products = ref([]);
const isSuccessModalOpen = ref(false);
const isAlertModalOpen = ref(false);
const message = ref("");
const appliedCoupon = ref(null);
const cash = ref(0);
const custom_discount = ref(0);
const isSelectModalOpen = ref(false);
// const balance = ref(0);
const billCategory = ref("");
const orderId = ref("");
const discount = ref(0);

const handleModalOpenUpdate = (newValue) => {
  isSuccessModalOpen.value = newValue;
  if (!newValue) {
    refreshData();
  }
};

const props = defineProps({
  loggedInUser: Object, // Using backend product name to avoid messing with selected products
  allcategories: Array,
  allemployee: Array,
  colors: Array,
  sizes: Array,
  sale: Object,
});

watch(
  () => props.sale.sale_items, // Watch the saleItems in the sale object
  (newSaleItems) => {
    // console.log(newSaleItems);
    if (newSaleItems) {
      // Map saleItems into the products array
      products.value = newSaleItems.map((item) => ({
        id: item.product_id,
        name: item.product.name, // Assuming product is nested under saleItem
        quantity: item.quantity,
        selling_price: item.unit_price,
        selected: item.status === "sold",
        status: item.status,
        cost_price: item.product.cost_price,
        discount: item.discount,
      }));
    }
  },
  { immediate: true } // Run immediately when the component is mounted
);

watch(
  () => props.sale, // Watch the saleItems in the sale object
  (newSale) => {
    console.log(newSale);
    if (newSale) {
      orderId.value = newSale?.order_id;
      custom_discount.value = newSale?.custom_discount;
      discount.value = newSale?.discount;
      cash.value = newSale?.cash;
    }
  },
  { immediate: true } // Run immediately when the component is mounted
);

const customer = ref({
  name: "",
  countryCode: "",
  contactNumber: "",
  email: "",
});

const employee_id = ref("");

const selectedPaymentMethod = ref("cash");

const refreshData = () => {
  router.visit(route("pos.index"), {
    preserveScroll: false, // Reset scroll
    preserveState: false, // Reset component state
  });
};

const removeCoupon = () => {
  appliedCoupon.value = null; // Clear the applied coupon
  couponForm.code = ""; // Clear the coupon field
};

// const orderId = computed(() => {
//   const timestamp = Date.now().toString(36).toUpperCase(); // Convert timestamp to a base-36 string
//   const randomString = Math.random().toString(36).substr(2, 5).toUpperCase(); // Generate a shorter random string
//   return `ORD-${timestamp}-${randomString}`; // Combine to create unique order ID
// });
// const orderId = computed(() => {
//   const characters =
//     "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
//   return Array.from({ length: 6 }, () =>
//     characters.charAt(Math.floor(Math.random() * characters.length))
//   ).join("");
// });

const submitOrder = async () => {
  // if (window.confirm("Are you sure you want to confirm the order?")) {
  if (balance.value < 0) {
    isAlertModalOpen.value = true;
    message.value = "Cash is not enough";
    return;
  }

  products.value = products.value.map((product) => ({
    ...product,
    selected: product.selected ?? false, // Add `selected: false` if not present
  }));

  try {
    // console.log(products.value);
    const response = await axios.post("/update_order", {
      customer: customer.value,
      products: products.value,
      employee_id: employee_id.value,
      paymentMethod: selectedPaymentMethod.value,
      userId: props.loggedInUser.id,
      orderId: orderId.value,
      cash: cash.value,
      custom_discount: custom_discount.value,
      billCategory: billCategory.value,
    });
    // isSuccessModalOpen.value = true;
    // console.log(response.data); // Handle success
  } catch (error) {
    if (error.response.status === 423) {
      isAlertModalOpen.value = true;
      message.value = error.response.data.message;
    }
    console.error(
      "Error submitting customer details:",
      error.response?.data || error.message
    );
    // alert("Failed to submit customer details. Please try again.");
  }
};
// };

const subtotal = computed(() => {
  return products.value
    .reduce(
      (total, item) => total + parseFloat(item.selling_price) * item.quantity,
      0
    )
    .toFixed(2); // Ensures two decimal places
});

const totalDiscount = computed(() => {
  const productDiscount = products.value.reduce((total, item) => {
    // Check if item has a discount
    if (item.discount && item.discount > 0 && item.apply_discount == true) {
      const discountAmount =
        (parseFloat(item.selling_price) - parseFloat(item.discounted_price)) *
        item.quantity;
      return total + discountAmount;
    }
    return total; // If no discount, return total as-is
  }, 0); // Ensures two decimal places

  const couponDiscount = appliedCoupon.value
    ? Number(appliedCoupon.value.discount)
    : 0;

  return (productDiscount + couponDiscount).toFixed(2);
});

const total = computed(() => {
  // Ensure subtotal and totalDiscount are numbers before performing calculations
  const subtotalValue = parseFloat(subtotal.value);
  const discountValue = parseFloat(totalDiscount.value);
  const customValue = parseFloat(custom_discount.value);

  // Subtract totalDiscount from subtotal to get the total
  return (subtotalValue - discountValue - customValue).toFixed(2);
});

const balance = computed(() => {
  if (cash.value == null || cash.value === 0) {
    return 0; // If cash.value is null or 0, return 0
  }
  return (parseFloat(cash.value) - parseFloat(total.value)).toFixed(2);
});
// Check for product or handle errors
const form = useForm({
  employee_id: "",
  barcode: "", // Form field for barcode
});

const couponForm = useForm({
  code: "",
});

// Temporary barcode storage during scanning
let barcode = "";
let timeout; // Timeout to detect the end of the scan

const submitCoupon = async () => {
  try {
    const response = await axios.post(route("pos.getCoupon"), {
      code: couponForm.code, // Send the coupon field
    });

    const { coupon: fetchedCoupon, error: fetchedError } = response.data;

    if (fetchedCoupon) {
      appliedCoupon.value = fetchedCoupon;
      products.value.forEach((product) => {
        product.apply_discount = false;
      });
    } else {
      isAlertModalOpen.value = true;
      message.value = fetchedError;
      error.value = fetchedError;
    }
  } catch (err) {
    // console.error(error);
    if (err.response.status === 422) {
      isAlertModalOpen.value = true;
      message.value = err.response.data.message;
    }
  }
};

// Automatically submit the barcode to the backend
const submitBarcode = async () => {
  try {
    // Send POST request to the backend
    const response = await axios.post(route("pos.getProduct"), {
      barcode: form.barcode, // Send the barcode field
    });

    // Extract the response data
    const { product: fetchedProduct, error: fetchedError } = response.data;

    if (fetchedProduct) {
      if (fetchedProduct.stock_quantity < 1) {
        isAlertModalOpen.value = true;
        message.value = "Product is out of stock";
        return;
      }
      // Check if the product already exists in the products array
      const existingProduct = products.value.find(
        (item) => item.id === fetchedProduct.id
      );

      if (existingProduct) {
        // If it exists, increment the quantity
        existingProduct.quantity += 1;
      } else {
        // If it doesn't exist, add it to the products array with quantity 1
        products.value.push({
          ...fetchedProduct,
          quantity: 1,
          apply_discount: false, // Add the new attribute
        });
      }

      product.value = fetchedProduct; // Update product state for individual display
      error.value = null; // Clear any previous errors
      console.log(
        "Product fetched successfully and added to cart:",
        fetchedProduct
      );
    } else {
      isAlertModalOpen.value = true;
      message.value = fetchedError;
      error.value = fetchedError; // Set the error message
      console.error("Error:", fetchedError);
    }
  } catch (err) {
    if (err.response.status === 422) {
      isAlertModalOpen.value = true;
      message.value = err.response.data.message;
    }

    console.error("An error occurred:", err.response?.data || err.message);
    error.value = "An unexpected error occurred. Please try again.";
  }
};

// Handle input from the barcode scanner
const handleScannerInput = (event) => {
  clearTimeout(timeout); // Clear the timeout for each keypress
  if (event.key === "Enter") {
    // Barcode scanning completed
    form.barcode = barcode; // Set the scanned barcode into the form
    submitBarcode(); // Automatically submit the barcode
    barcode = ""; // Reset the barcode for the next scan
  } else {
    // Append the pressed key to the barcode
    barcode += event.key;
  }

  // Timeout to reset the barcode if scanning is interrupted
  timeout = setTimeout(() => {
    barcode = "";
  }, 100); // Adjust timeout based on scanner speed
};

// Attach the keypress event listener when the component is mounted
onMounted(() => {
  document.addEventListener("keypress", handleScannerInput);
  console.log(props.products);
});

const applyDiscount = (id) => {
  products.value.forEach((product) => {
    if (product.id === id) {
      product.apply_discount = true;
    }
  });
};

const removeDiscount = (id) => {
  products.value.forEach((product) => {
    if (product.id === id) {
      product.apply_discount = false;
    }
  });
};

const handleSelectedProducts = (selectedProducts) => {
  selectedProducts.forEach((fetchedProduct) => {
    const existingProduct = products.value.find(
      (item) => item.id === fetchedProduct.id
    );

    if (existingProduct) {
      // If the product exists, increment its quantity
      existingProduct.quantity += 1;
    } else {
      // If the product doesn't exist, add it with a default quantity
      products.value.push({
        ...fetchedProduct,
        quantity: 1,
        apply_discount: false, // Default additional attribute
      });
    }
  });
};
</script>
