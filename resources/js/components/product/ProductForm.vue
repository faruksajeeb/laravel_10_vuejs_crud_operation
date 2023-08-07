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
      <form
        id="form"
        @submit.prevent="submitForm"
        enctype="multipart/form-data"
        class="row g-3 needs-validation"  
        novalidate      
      >
        <div class="form-group">
          <label for="">Category <span class="text-danger">*</span>:</label>
          <select
            v-model="product.category_id"
            id=""
            name="category_id"
            class="form-select"
            :class="{ 'is-invalid': errors.category_id }"
            required
          >
            <option value="">--choose category--</option>
            <option :value="category.id" v-for="(category, index) in formatedCategories">
              {{ category.category_name }}
            </option>
          </select>
          <div class="invalid-feedback">{{ errors.category_id }}</div>
        </div>
        <div class="mb-3">
          <label for="name" class="form-label"
            >Name <span class="text-danger">*</span>:</label
          >
          <input
            class="form-control"
            type="text"
            id="name"
            name="name"
            v-model="product.name"
            :class="{ 'is-invalid': errors.name }"
            required
          />
          <div class="invalid-feedback">{{ errors.name }}</div>
        </div>
        <div class="mb-3">
          <label for="description" class="form-label">Description:</label>
          <textarea
            class="form-control"
            id="description"
            name="description"
            v-model="product.description"
            
          ></textarea>
        </div>
        <div class="mb-3">
          <label for="price" class="form-label"
            >Price <span class="text-danger">*</span>:</label
          >
          <input
            class="form-control"
            type="number"
            id="price"
            name="price"
            v-model="product.price"
            
          />
          <div class="invalid-feedback">Please enter product name.</div>
        </div>
        <div class="mb-3">
          <div class="row">
            <div class="col-md-8">
              <label for="price" class="form-label"
                >Feature Image <span class="text-danger">*</span>:</label
              >
              <input
                class="form-control"
                type="file"
                accept="image/*"
                name="file"
                id="file"
                @change="onFileChange"
                :class="{ 'is-invalid': errors.file }"
              />
              <div class="invalid-feedback">{{ errors.file }}</div>
            </div>
            <div class="col-md-4 image-container">
              <div class="image-item">
                <img v-if="imageUrl" :src="imageUrl" alt="Image Preview" width="150" />
                <button
                  class="remove-button"
                  type="button"
                  v-if="imageUrl"
                  @click="removeSingleImage(image, key)"
                >
                  x
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="form-group image-gallery">
          <label class="file-label">
            <span>Choose Gallery Images</span>
            <input
              type="file"
              accept="image/*"
              id="file-upload"
              name="gallery_images[]"
              class="custom-file-input"
              multiple
              @change="handleMultipleFileUpload"
            />
          </label>
          <p v-if="images.length > 0">{{ selectedFileMessage }}</p>
          <div class="image-container">
            <div v-for="(image, index) in images" :key="index" class="image-item">
              <img
                :src="image.previewUrl"
                alt="Preview"
                class="profile-pic"
                @click="showImage(index)"
              />
              <button class="remove-button" @click="removeImage(index)">X</button>
            </div>
          </div>
          <div v-if="showModal" class="modal" @click.self="closeModal">
            <div class="modal-content">
              <span class="close" @click="closeModal">&times;</span>
              <img :src="selectedImage.previewUrl" alt="Preview" class="modal-image" />
            </div>
          </div>
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
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      imageUrl: null,
      images: [],
      showModal: false,
      selectedImage: null,
      product: {
        category_id: "",
        name: "",
        description: "",
        price: 0,
        file: "",
      },
      errors: {
        category_id: "",
        name: "",
        file: "",
      },
      validationErrors: "",
    };
  },
  computed: {
    selectedFileMessage() {
      if (this.images.length == 0) {
        return "";
      } else if (this.images.length == 1) {
        return "1 file selected!";
      } else {
        return this.images.length + " files selected!";
      }
    },
    isNewProduct() {
      return !this.$route.path.includes("edit");
    },
    categories() {
      return this.$store.state.categories;
    },
    // formatedCategories(){
    //   return this.$store.getters.formatedCategories;
    // },
    ...mapGetters(["formatedCategories"]),
  },
  async created() {
    if (!this.isNewProduct) {
      const response = await axios.get(`/api/products/${this.$route.params.id}`);
      this.product = response.data;
    }
  },
  methods: {
    handleMultipleFileUpload(event) {
      const files = event.target.files;
      for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.images.push({
            file: files[i],
            previewUrl: e.target.result,
          });
        };
        reader.readAsDataURL(files[i]);
      }
    },
    removeImage(index) {
      this.images.splice(index, 1);
    },
    showImage(index) {
      this.selectedImage = this.images[index];
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.selectedImage = null;
    },
    onFileChange(e) {
      // console.log(e.target.files[0]);
      this.product.file = e.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.imageUrl = e.target.result;
      };
      reader.readAsDataURL(this.product.file);
    },
    removeSingleImage(image, index) {
      this.imageUrl = null;
      this.product.file = null;
    },
    async submitForm() {
      try {
        this.clearErrors();

        if (!this.product.category_id) {
          this.errors.category_id = "Please choose a category.";
        }
        if (!this.product.name) {
          this.errors.name = "Please enter a name.";
        }

        if (!this.product.file) {
          this.errors.file = "Please choose a file.";
        }

        if (this.hasErrors()) {
          return;
        }
        if (this.isNewProduct) {
          const form = document.getElementById("form");
          let formData = new FormData(form);
          // formData.append('file', this.file);
          // formData.append('product_name', this.product.name);
          //  console.log(formData);
          //  console.log(...formData);
          //  return false;
          await axios
            .post("/api/products", formData)
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
        console.log(error);
      }
    }, 
    clearErrors() {
      this.errors.category_id = '';
      this.errors.name = '';
      this.errors.file = '';
    },
    hasErrors() {
      return this.errors.category_id || this.errors.name || this.errors.file;
    }
  },
};
</script>

<style scope>
.custom-file-input {
  opacity: 0;
  width: 200px;
  /* height: 200px; */
  /* height: 100%; */
  position: absolute;
  top: 0;
  left: 0;
  cursor: pointer;
}
.file-label {
  display: inline-block;
  background-color: #f9f6f6;
  color: #000;
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
  cursor: pointer;
}

.file-label:hover {
  background-color: #dbdada;
}
.image-gallery {
  display: flex;
  flex-direction: column;
  align-items: left;
  justify-content: top;
  /* height: 100vh; */
  height: auto;
  font-family: Arial, sans-serif;
}

/* Style for the file input label */
.image-container {
  display: flex;
  flex-wrap: wrap;
  margin-top: 20px;
}

.image-item {
  position: relative;
  margin-right: 10px;
  margin-bottom: 10px;
}

.profile-pic {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 5%;
  cursor: pointer;
}

.remove-button {
  position: absolute;
  top: 5px;
  right: 5px;
  padding: 5px;
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-weight: bold;
}

.remove-button:hover {
  background-color: #d32f2f;
}

/* Modal styles */
.modal {
  display: block;
  position: fixed;
  z-index: 1;
  padding-top: 50px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0, 0, 0, 0.8);
}

.modal-content {
  margin: auto;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 80%;
  max-width: 800px;
  height: 80%;
}

.modal-image {
  width: 100%;
  height: auto;
  object-fit: contain;
}

.close {
  position: absolute;
  top: 20px;
  right: 30px;
  font-size: 35px;
  font-weight: bold;
  color: #f1f1f1;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}
</style>
