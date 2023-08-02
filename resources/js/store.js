import { createStore } from 'vuex'

// Create a new store instance.
const store = createStore({
  state () {
    return {
      categories : [
        {id:1,category_name:'mobile'},
        {id:2,category_name:'phone'},
        {id:3,category_name:'laptop'},
        {id:4,category_name:'bag'},
        {id:5,category_name:'sunglass'},
      ]
    }
  },
  getters : {
    formatedCategories : (state)=>{
        let formatedCategory = state.categories.map((category)=>{
            const str = `${category.category_name}`;
            const formatedName = str.charAt(0).toUpperCase() + str.slice(1);
            return {
                id : category.id,
                category_name : formatedName
            }
        });
        return formatedCategory;
    }
  }
})
export default store
