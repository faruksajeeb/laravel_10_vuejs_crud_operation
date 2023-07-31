<script>
import axios from "axios";
export default {
  name: "products",
  data() {
    return {
      totalRecords: 0,
      products: {
        type: Object,
        default: null,
      },
    };
  },
  mounted() {
    this.getProducts();
  },
  methods: {
    async getProducts(page = 1) {
      try {
        await axios
          .get(`/api/products?page=${page}`)
          .then(({ data }) => {
            this.totalRecords = data.data.length;
            this.products = data;
          })
          .catch(({ response }) => {
            console.error(response);
          });
      } catch (error) {
        console.error(error);
      }
    },
    async deleteProduct(id) {
      try {
        await axios.delete(`/api/products/${id}`);
        this.products = this.products.filter((product) => product.id !== id);
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
<template>
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12">
          <router-link
            to="/products/create"
            class="btn btn-outline-primary btn-sm float-end"
            >Add Product</router-link
          >
        </div>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col">Price</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody v-if="totalRecords > 0">
            <!-- <tbody > -->
            <tr v-for="(product, index) in products.data" :key="index">
              <td>{{ product.id }}</td>
              <td>{{ product.name }}</td>
              <td>{{ product.description }}</td>
              <td>{{ product.price }}</td>
              <td>
                <div class="row gap-3">
                  <router-link
                    :to="`/products/${product.id}`"
                    class="p-1 col border btn btn-sm btn-primary"
                    >View</router-link
                  >
                  <router-link
                    :to="`/products/${product.id}/edit`"
                    class="p-1 col border btn btn-sm btn-success"
                    >Edit</router-link
                  >
                  <button
                    @click="deleteProduct(product.id)"
                    type="button"
                    class="p-1 col border btn btn-sm btn-danger"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="5">
                <span
                  class="spinner-border spinner-border-sm"
                  role="status"
                  aria-hidden="true"
                ></span>
                Loading...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <pagination
        align="center"
        :data="products"
        :limit="5"
        @pagination-change-page="getProducts"
      ></pagination>
    </div>
  </div>
</template>
<style scoped>
.pagination {
  margin-bottom: 0;
}
</style>
