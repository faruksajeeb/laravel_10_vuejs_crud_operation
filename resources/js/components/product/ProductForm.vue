<template>
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <h2 v-if="isNewProduct">Add Product</h2>
      <h2 v-else>Edit Product</h2>
      <div v-if="validationErrors">
        <ul class="alert alert-danger p-4">
          <li v-for="(value, key, index) in validationErrors">@{{ value }}</li>
        </ul>
      </div>
      <form @submit.prevent="submitForm" class="row g-3 needs-validation" novalidate>
        <div class="mb-3">
          <label for="name" class="form-label">Name:</label>
          <input
            class="form-control"
            type="text"
            id="name"
            v-model="product.name"
            required
          />
          <div class="invalid-feedback">Please enter product name.</div>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description:</label>
          <textarea
            class="form-control"
            id="description"
            v-model="product.description"
            required
          ></textarea>
        </div>
        <div class="mb-3">
          <label for="price" class="form-label">Price:</label>
          <input
            class="form-control"
            type="number"
            id="price"
            v-model="product.price"
            required
          />
          <div class="invalid-feedback">Please enter product name.</div>
        </div>
        <button type="submit" v-if="isNewProduct" class="btn btn-primary">
          Add Product
        </button>
        <button type="submit" v-else class="btn btn-primary">Update Product</button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      product: {
        name: "",
        description: "",
        price: 0,
      },
      validationErrors: "",
    };
  },
  computed: {
    isNewProduct() {
      return !this.$route.path.includes("edit");
    },
  },
  async created() {
    if (!this.isNewProduct) {
      const response = await axios.get(`/api/products/${this.$route.params.id}`);
      this.product = response.data;
    }
  },
  methods: {
    async submitForm() {
      try {
        if (this.isNewProduct) {
          await axios
            .post("/api/products", this.product)
            .then((result) => {
              console.log(result);
              if (result.status == 200 && result.data.status == "success") {
                this.$swal("INSERTED!", `${result.data.message}`, "success");
                this.$router.push("/products");
              } else {
                this.$swal("ERROR!", `${result.data.message}`, "error");
              }
            })
            .catch((error) => {
              if (error.response.status == 422) {
                this.validationErrors = error.response.data.errors;
              } else {
                this.$swal("ERROR!", `${result.data.message}`, "error");
              }
            })
            .finally(() => {
              // always executed;
            });
        } else {
          await axios
            .put(`/api/products/${this.$route.params.id}`, this.product)
            .then((response) => {
              this.$swal("UPDATED!", "Data has been updated successfully!", "success");
              this.$router.push("/products");
            });
        }
      } catch (error) {
        console.error(error);
      }
    },
  },
};
</script>
