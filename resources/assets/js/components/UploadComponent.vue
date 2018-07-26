<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header font-weight-bold">Gallery</div>

                    <div class="card-body">

                        <div class="row justify-content-center">
                            <div class="upload text-center" v-bind:class="{'upload-dragover' : isDragover}"
                                 @dragover="ondragover"
                                 @dragleave="ondragleave"
                                 @drop="ondrop"
                                 @click="onclick">

                                <i class="fas fa-cloud-upload-alt fa-5x text-secondary"></i>
                                <p class="text-secondary">Drop files here or click to choose files...</p>
                                <div class="wrapper">
                                    <input type="file" name="files" id="files" multiple ref="files" @change="onchange">
                                </div>
                            </div>
                        </div>

                        <gallery-list-component :medias="medias" v-on:delete-media="deleteMedia"></gallery-list-component>
                    </div>
                </div>
            </div>
        </div>
        <div id="myModal" class="modal fade">
            <img class="modal-dialog" id="img-popup">
            <span class="close" data-dismiss="modal">&times;</span>
        </div>
    </div>
</template>

<script>
    import GalleryListComponent from './GalleryComponent';

    export default {
        data : function(){
            return {
                isDragover : false,
                medias : [],
            }
        },

        components : {
            GalleryListComponent
        },

        created(){
            this.fetchData();
        },

        methods : {
            ondragover : function(e) {
                e.preventDefault();
                this.isDragover = true;
            },
            ondragleave : function (e) {
                e.preventDefault();
                this.isDragover = false;
            },
            ondrop : function (e){
                e.preventDefault();
                this.isDragover = false;

                const files = e.dataTransfer.files;
                this.handleUpload(files);

            },
            onclick : function (e) {
                this.$refs.files.click();
            },
            onchange : function(e){
                const files = e.target.files;
                this.handleUpload(files);
            },
            handleUpload : function (files){

                Array.from(files).forEach(function(file){
                    let fileInfo = {
                        'id' : '',
                        'name' : file.name,
                        'media' : '',
                        'isImage' : file.type == 'image/jpeg' || file.type == 'image/png' ? true : false,
                        'isOverMaxSize' : file.size > 10485760 ? true : false,
                        'isUpload' : true,
                        'progessPercentage' : 0,
                    };

                    this.medias.push(fileInfo);
                    if( fileInfo.isImage && !fileInfo.isOverMaxSize){
                        const formData = new FormData();
                        formData.append('files[]', file);

                        axios.post('media-upload', formData, { onUploadProgress: progressEvent => {
                                fileInfo.progessPercentage = parseInt( Math.round( ( progressEvent.loaded / progressEvent.total * 100)));
                            }
                        })
                            .then(response => {
                                fileInfo.id = response.data.data.id;
                                fileInfo.name = response.data.data.name;
                                fileInfo.media = response.data.data.media;
                                fileInfo.isImage = response.data.data.isImage;
                                fileInfo.isOverMaxSize = response.data.data.isOverMaxSize;
                                fileInfo.isUpload = response.data.data.isUpload;
                                fileInfo.progessPercentage = response.data.data.progessPercentage;
                            });
                    }
                }.bind(this));

                this.$refs.files.value = '';

            },
            fetchData : function(){
                axios.get('gallery')
                    .then(response => {
                        this.medias = response.data.data;
                    });
            },
            deleteMedia : function(id, index){
                this.medias.splice(index, 1);
                if(id != ''){
                    axios.delete('media-upload/'+id)
                        .then(response => {
                            console.log(response.data.msg);
                        });
                }
            }

        }
    }
</script>

<style lang="scss" scoped>
    .upload {
        margin-left: 15px;
        margin-right: 15px;
        padding: 50px 45px;
        width: 100%;
        border: 2px dashed #cecece;
    }

    .upload:hover{
        cursor: pointer;
    }

    .upload-dragover {
        border: 2px dashed #4e555b;
    }

    .wrapper{
        display: none;
    }

    .modal-dialog {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        padding: 70px 0;
    }

    .close {
        position: absolute;
        top: 80%;
        right: 100px;
        color: #f1f1f1;
        font-size: 70px;
        font-weight: bold;
        transition: 0.3s;
    }

    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

</style>