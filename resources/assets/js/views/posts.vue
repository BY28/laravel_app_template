<template>
    <div>
        <div class="container">
            
            <h2>Posts</h2>

            <div class="row">
                <form class="col s12" @submit.prevent @keydown="post.errors.clear($event.target.name)">
                    <div class="row">
                        <div class="input-field col s6">
                          <input name="title" placeholder="Title" id="title" type="text" class="validate" v-model="post.title">
                          <!-- <label for="title">Title</label> -->
                          <span class="brown-text" v-if="post.errors.has('title')" v-text="post.errors.get('title')"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                              <select class="browser-default" name="section" id="section" v-model="post.section">
                                <option value="" disabled selected>Choose a section</option>
                                <option v-for="section in sections" v-bind:key="section.id" v-bind:value="section.id">{{ section.name }}</option>
                              </select>
                          <!-- <label for="section">Section</label> -->
                          <span class="brown-text" v-if="post.errors.has('section')" v-text="post.errors.get('section')"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                             <div class="chips" id="post_tags">
                                <!-- <input class="validate"> -->
                             </div>
                          <!-- <label for="section">Section</label> -->
                          <span class="brown-text" v-if="post.errors.has('tags')" v-text="post.errors.get('tags')"></span>
                        </div>
                    </div>
                    <div class="row">
                         <div class="file-field input-field col s6">
                              <div style="display: none;" class="btn brown lighten-2">
                                <span>File</span>
                                <input type="file" multiple @change="onFileSelected" ref="fileInput">
                              </div>
                              
                              <div style="display: none;" class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Upload one or more files">
                              </div>
                              <button class="btn btn-lg brown lighten-2" @click="$refs.fileInput.click()">Add image</button>
                          
                            </div>

                            <div class="row">
                                <div v-for="(file, file_index) in imageFiles" v-bind:key="file_index" >
                                      <img class="responsive-img" :src="fileUrl(file)" width="20%">
                                      <a href="#" @click="imageFiles.splice(file_index, 1)"><i class="material-icons">delete</i></a>
                                </div>
                            </div>
                    </div>
                     <div class="row">
                        <div class="input-field col s6">
                          <textarea name="body" placeholder="Body" id="textarea" class="materialize-textarea" v-model="post.body"></textarea>
                          <span class="brown-text" v-if="post.errors.has('body')" v-text="post.errors.get('body')"></span>
                        </div>
                     </div>
                     <div class="row">
                         <button v-if="!isEdit" class="btn brown lighten-2" type="submit" name="action" @click="store" :disabled="post.errors.any()">Submit<i class="material-icons right">send</i></button>
                         <button v-if="isEdit" class="btn brown lighten-2" type="submit" name="action" @click="update" :disabled="post.errors.any()">Edit<i class="material-icons right">send</i></button>
                     </div>
                </form>
            </div>
        
            <transition-group name="router-anim" tag="div">
                <div class="row" v-for="(post, index) in posts" v-bind:key="post.id">
                    <div class="col s12 m12">
                      <div class="card brown lighten-3 hoverable">
                          <div class="card">
                            <div class="card-image">

                                
                                <div v-for="(image, img_index) in post.images" v-bind:key="image.id" >
                                      <img class="responsive-img" :src="imageUrl(image.name)">
                                      <input type="file"  @change="onEditFileSelected($event, image, index, img_index)" ref="imgFileInput">
                                      <a href="#" @click="delete_image(image.id, index, img_index)"><i class="material-icons">delete</i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-content white-text">
                          <span class="card-title"><router-link :to="{name: 'post', params: {slug: post.slug}}">{{ post.title }}</router-link></span>
                          <span>{{ post.section.name }}</span>
                          <p>{{ post.body }}</p>
                          <span v-for="tag in post.tags" v-bind:key="tag.id">{{tag.name}}</span>
                        </div>
                        <div class="card-action">
                          <a class="white-text" href="#" @click="destroy(post.id, index)"><i class="material-icons">delete</i></a>
                          <a class="white-text" href="#" @click="edit(post, index)"><i class="material-icons">edit</i></a>
                        </div>
                      </div>
                    </div>
                </div>
            </transition-group>
            <ul class="pagination">
                <li v-bind:class="[{disabled: !pagination.prev_page_url}]"><a href="#!"><i class="material-icons" @click="navigate(pagination.prev_page_url)">chevron_left</i></a></li>
                <li class="disabled"><a href="#!">{{pagination.current_page}}/{{pagination.last_page}}</a></li>
                <li v-bind:class="[{disabled: !pagination.next_page_url}]"><a href="#!" @click="navigate(pagination.next_page_url)"><i class="material-icons">chevron_right</i></a></li>
             </ul>
        </div>
    
    </div>
</template>

<script>
    export default{

        created()
        {
            this.$store.dispatch('posts/index');
        },

        mounted(){
            this.initChips();
        },

        computed: {
                ...mapGetters({
                        sections: 'posts/sections',
                        tagsName: 'posts/tagsName',
                        posts: 'posts/data',
                        post: 'posts/form',
                        chips: 'posts/chips',
                        isEdit: 'posts/isEdit',
                        formData: 'posts/formData',
                        selectedFiles: 'posts/selectedFiles',
                        imageFiles: 'posts/imageFiles',
                        post_index: 'posts/current_index',
                        api_url: 'posts/api_url',
                        pagination: 'posts/pagination',
                }),
        },

        methods: {
            index(){
                this.$store.dispatch('posts/getSections');
                this.$store.dispatch('posts/getTags');
                this.$store.dispatch('posts/getPosts');
            },
            navigate(page_url){
                this.$store.dispatch('posts/navigate', page_url);
            },
            store(){
                this.$store.dispatch('posts/store');
            },
            update(){
                this.$store.dispatch('posts/update');
            },
            edit(post, index){
                this.$store.commit('posts/edit', {element: post, index: index});
            },
            destroy(id, index){
                this.$store.dispatch('posts/destroy', {id: id, index: index});
            },
            onFileSelected(event)
            {
                this.$store.commit('posts/onFileSelected', event);
            },
            imageUrl(name)
            {
                return 'uploads/posts/'+name;
            },
            fileUrl(file)
            {
                return URL.createObjectURL(file);
            },
            onEditFileSelected(event, image, post_index, img_index)
            {
                this.$store.dispatch('posts/updateFile', {event: event, current_index: post_index, img_index: img_index, image: image});
            },

            delete_image(id, post_index, img_index)
            {
                this.$store.dispatch('posts/deleteFile', {id: id, current_index: post_index, img_index: img_index});
            },

            initChips()
            {
                this.$nextTick(function () {
                    var elem = document.getElementById('post_tags');
                
                    var options = {
                        autocompleteOptions: {
                        data: {
                            /*'Apple': null,
                            'Microsoft': null,
                            'Google': null*/
                          },
                          limit: Infinity,
                          minLength: 1
                        },
                         placeholder: 'Enter a tag',
                         secondaryPlaceholder: '+Tag',
                    }
                    let instance = M.Chips.init(elem, options);
                    this.$store.commit('posts/setChips', instance);
                });
            }
        }
    }
</script>

<style type="text/css">
.pagination li.active {
    background-color: #a1887f;
}
.chip:active, .chip:focus{
    background-color: #a1887f !important;
}
</style>