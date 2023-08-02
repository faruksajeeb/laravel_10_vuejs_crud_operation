<script>
import axios from "axios";
export default {
  name: "products",
  data() {
    return {
      checked: [],
      paginator: {
        totalRecords: 0,
        from: 0,
        to: 0,
        current_page: "",
        per_page: "",
      },
      products: {
        type: Object,
        default: null,
      },
      params: {
        paginate: 5,
        id: "",
        category_id: "",
        sort_field: "created_at",
        sort_direction: "desc",
        id: "",
        name: "",
        description: "",
        price: "",
      },
      search: "",
    };
  },
  mounted() {
    this.getProducts();
  },
  watch: {
    params: {
      handler() {
        this.getProducts();
      },
      deep: true,
    },
    search(val, old) {
      if (val.length >= 3 || old.length >= 3) {
        this.getProducts();
      }
    },
    // category_id(value){this.getProducts()}
  },
  methods: {
    async getProducts(page = 1) {
      await axios
        // .get(`/api/products?page=${page}`)
        // .get(`/api/products?page=${page}&category_id=${this.params.category_id}&sort_field=${this.params.sort_field}&sort_direction=${this.params.sort_direction}`)
        .get("/api/products", {
          params: {
            page,
            search: this.search.length >= 3 ? this.search : "",
            ...this.params,
          },
        })
        .then((response) => {
          // console.log(response);
          this.products = response.data;
          this.paginator.totalRecords = response.data.total;
          if (response.data.total <= 0) {
            document.querySelector(".loading-section").innerText = "No Record Found!.";
          }
          this.paginator.from = response.data.from;
          this.paginator.to = response.data.to;
          this.paginator.current_page = response.data.current_page;
          this.paginator.per_page = response.data.per_page;
        });
    },
    refreshData(){
      this.getProducts();
    },
    changeShort(field) {
      if (this.params.sort_field === field) {
        this.params.sort_direction =
          this.params.sort_direction === "asc" ? "desc" : "asc";
      } else {
        this.params.sort_field = field;
        this.params.sort_direction = "asc";
      }
      // this.getProducts();
    },
    deleteProduct(id) {
      this.$swal
        .fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!",
        })
        .then((result) => {
          if (result.value) {
            try {
              axios
                .delete(`/api/products/${id}`)
                .then((response) => {
                  this.getProducts();
                  this.$swal(
                    "DELETED!",
                    "Data has been deleted successfully!",
                    "success"
                  );
                })
                .catch((error) => {
                  this.$swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: `${error}`,
                  });
                });
            } catch (error) {
              this.$swal("ERROR!", `${error}`, "error");
              // console.error(error);
            }
          }
        });
    },
    downloadFile() {
      let loader =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" ></span> Exporting...';
      document.querySelector(".export-btn").innerHTML = loader;
      try {
        axios
          // .get("/api/products-export")
          .get("/api/products-export", { responseType: "arraybuffer" })
          .then((response) => {
            if (response.status == 200) {
              document.querySelector(".export-btn").innerText = "Export to Excel";
              this.$swal("Exported!",'Exported Successfully', "success");
              var fileURL = window.URL.createObjectURL(new Blob([response.data]));
              var fileLink = document.createElement("a");
              fileLink.href = fileURL;
              fileLink.setAttribute("download", "product_list.xlsx");
              document.body.appendChild(fileLink);
              fileLink.click();
            } else {
              this.$swal("ERROR!", `${response.data.message}`, "error");
            }
          });
      } catch (error) {
        this.$swal("ERROR!", `${error}`, "error");
        // console.error(error);
      }
    },
    exportPdf() {
      let loader =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" ></span> PDF Exporting...';
      document.querySelector(".export-btn-pdf").innerHTML = loader;
      axios.get("/api/products-export-pdf", { responseType: "blob" }).then((response) => {
        document.querySelector(".export-btn-pdf").innerText = "Export PDF";
        this.$swal("Exported!",'Exported Successfully', "success");
        var fileURL = window.URL.createObjectURL(
          new Blob([response.data], { type: "application/pdf" })
        );
        var fileLink = document.createElement("a");
        fileLink.href = fileURL;
        fileLink.setAttribute("download", "product_list.pdf");
        document.body.appendChild(fileLink);
        fileLink.click();
      });
    },
  },
};
</script>
<template>
  <div class="row mb-2">
    <div class="col-md-12">
      
      <div class="input-group">
        <button @click="downloadFile" class="btn btn-primary export-btn">
          Export to Excel
        </button>
        <button @click="exportPdf" class="btn btn-danger export-btn-pdf">Export PDF</button>
        <button @click="refreshData" class="btn btn-warning refresh-btn">Refresh Data</button>
        <router-link to="/products/create" class="btn btn-outline-primary float-end"
        >Add Product</router-link
      >
      </div>
      
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="row justify-content-between">
        <div class="input-group">
          <div class="d-flex col-md-2">
            <label for="">Per Page: </label>
            <select v-model="params.paginate" id="" class="form-select-sm">
              <option value="5" selected>5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
          <select v-model="params.category_id" id="" class="form-select">
            <option value="">--choose category--</option>
            <option value="1">Mobile</option>
            <option value="2">Laptop</option>
            <option value="3">Book</option>
            <option value="4">T-shart</option>
          </select>
          <input
            type="text"
            class="form-control"
            placeholder="Search by name,des,price. (Type and Enter)"
            v-model.lazy="search"
          />
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th scope="col">
                <input type="checkbox" class="form-check-input" />
              </th>
              <th scope="col">
                <a href="#" @click.prevent="changeShort('id')">#ID</a>
                <span
                  v-if="
                    this.params.sort_field == 'id' && this.params.sort_direction == 'asc'
                  "
                  >↑</span
                >
                <span
                  v-if="
                    this.params.sort_field == 'id' && this.params.sort_direction == 'desc'
                  "
                  >↓</span
                >
              </th>
              <th scope="col">
                <a href="#" @click.prevent="changeShort('name')">Name</a>
                <!-- <a href="#">Name</a> -->
                <span
                  v-if="
                    this.params.sort_field == 'name' &&
                    this.params.sort_direction == 'asc'
                  "
                  >↑</span
                >
                <span
                  v-if="
                    this.params.sort_field == 'name' &&
                    this.params.sort_direction == 'desc'
                  "
                  >↓</span
                >
              </th>
              <th scope="col">
                <a href="#" @click.prevent="changeShort('description')">Description</a>
                <!-- <a href="#">Description</a> -->
                <span
                  v-if="
                    this.params.sort_field == 'description' &&
                    this.params.sort_direction == 'asc'
                  "
                  >↑</span
                >
                <span
                  v-if="
                    this.params.sort_field == 'description' &&
                    this.params.sort_direction == 'desc'
                  "
                  >↓</span
                >
              </th>

              <th scope="col">
                <a href="#" @click.prevent="changeShort('price')">Price</a>
                <!-- <a href="#">Price</a> -->
                <span
                  v-if="
                    this.params.sort_field == 'price' &&
                    this.params.sort_direction == 'asc'
                  "
                  >↑</span
                >
                <span
                  v-if="
                    this.params.sort_field == 'price' &&
                    this.params.sort_direction == 'desc'
                  "
                  >↓</span
                >
              </th>
              <th scope="col">Actions</th>
            </tr>
            <tr>
              <th></th>
              <th>
                <input
                  type="text"
                  placeholder="Search By ID"
                  class="form-control"
                  v-model="params.id"
                />
              </th>
              <th>
                <input
                  type="text"
                  placeholder="Search By Name"
                  class="form-control"
                  v-model="params.name"
                />
              </th>
              <th>
                <input
                  type="text"
                  placeholder="Search By Description"
                  class="form-control"
                  v-model="params.description"
                />
              </th>
              <th>
                <input
                  type="text"
                  placeholder="Search By Price"
                  class="form-control"
                  v-model="params.price"
                />
              </th>
              <th></th>
            </tr>
          </thead>
          <tbody v-if="products && paginator.totalRecords > 0">
            <!-- <tbody > -->
            <tr v-for="product in products.data" :key="product.id">
              <td>
                <input
                  type="checkbox"
                  :value="product.id"
                  v-model="checked"
                  class="form-check-input"
                />
              </td>
              <td class="text-nowrap">{{ product.id }}</td>
              <td class="text-nowrap">{{ product.name }}</td>
              <td class="text-nowrap">{{ product.description.substring(0, 50) }}</td>
              <td class="text-nowrap">{{ product.price }}</td>
              <td class="text-nowrap">
                <!-- <div class="row gap-1"> -->
                <router-link
                  :to="`/products/${product.id}`"
                  class="btn btn-sm btn-primary m-1"
                  >View</router-link
                >
                <router-link
                  :to="`/products/${product.id}/edit`"
                  class="btn btn-sm btn-success m-1"
                  >Edit</router-link
                >
                <button
                  @click="deleteProduct(product.id)"
                  type="button"
                  class="btn btn-sm btn-danger m-1"
                >
                  Delete
                </button>
                <!-- </div> -->
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="6" class="text-center loading-section">
                <span
                  class="spinner-border spinner-border-lg"
                  role="status"
                  aria-hidden="true"
                ></span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="col-md-6">
          <p>
            Current Page: {{ paginator.current_page }} Per Page: {{ paginator.per_page }},
            Showing {{ paginator.from }} to {{ paginator.to }} of
            {{ paginator.totalRecords }} entries
          </p>
        </div>
        <div class="col-md-6">
          <pagination
            align="right"
            :data="products"
            :limit="5"
            @pagination-change-page="getProducts"
          ></pagination>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.pagination {
  margin-bottom: 0;
}
</style>
