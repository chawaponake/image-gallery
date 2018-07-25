<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header font-weight-bold">Gallery</div>

                    <div class="card-body">

                        <div class="row justify-content-center">
                            <div class="upload text-center" v-bind:class="{'upload-dragover' : isDragover}"
                                 v-on:dragover="ondragover"
                                 v-on:dragleave="ondragleave"
                                 v-on:drop="ondrop"
                                 v-on:click="onclick">

                                <i class="fas fa-cloud-upload-alt fa-5x text-secondary"></i>
                                <p class="text-secondary">Drop files here or click to choose files...</p>
                                <div class="wrapper">
                                    <input type="file" name="files" id="files" multiple ref="files" @change="onchange">
                                </div>
                            </div>
                        </div>
                        <div class="row align-content-center text-center gallery">
                            <div class="col-md-4" v-for="media in medias">
                                <div class="img-thumb-error" v-if="!media.isImage">
                                    <div class="overlay"></div>
                                    <i class="fas fa-exclamation-triangle text-danger fa-7x pt-4"></i>
                                    <p class="text-danger font-weight-bold">File type not supported. - {{media.name}}</p>
                                    <div class="img-thumb-button">
                                        <button type="button" class="btn btn-danger" @click="deleteMedia(media.id)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="img-thumb-error" v-if="media.isOverMaxSize">
                                        <div class="overlay"></div>
                                        <i class="fas fa-exclamation-triangle text-danger fa-7x pt-4"></i>
                                        <p class="text-danger font-weight-bold">File size exceeded. - {{media.name}}</p>
                                        <div class="img-thumb-button">
                                            <button type="button" class="btn btn-danger" @click="deleteMedia(media.id)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="img-thumb" v-else>
                                        <div class="overlay"></div>
                                        <div v-bind:class="{'uploading' : media.isUpload}">
                                            <img src="/image/loading.gif" v-if="media.isUpload"/>
                                            {{media.progessPercentage}}
                                        </div>
                                        <img :src="media.media" v-if="!media.isUpload">
                                        <div class="img-thumb-button">
                                            <button type="button" class="btn btn-primary" @click="showPopup(media.media)">
                                                <i class="fas fa-search"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger" @click="deleteMedia(media.id)">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    export default {
        data : function(){
            return {
                isDragover : false,
                medias : [],
            }
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
                }.bind(this));

                this.$refs.files = "";

            },
            fetchData : function(){
                axios.get('gallery')
                    .then(response => {
                        this.medias = response.data.data;
                    });
            },
            showPopup: function(url){
                $('#myModal').modal('show');
                $('#img-popup').attr('src',url);
            },
            deleteMedia : function(id){
                axios.delete('media-upload/'+id)
                    .then(response => {
                        this.fetchData();
                    })
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

    .gallery{
        padding-top: 20px;
    }

    .wrapper{
        display: none;
    }

    .img-thumb{
        width: 200px;
        height: 200px;

        img{
            width: 100%;
            height: auto;
        }
    }

    .img-thumb-error{
        width: 200px;
        height: 200px;
    }

    .overlay{
        position: absolute;
        width: 200px;
        height: 200px;
        padding: 10px 10px;

    }

    .img-thumb:hover .overlay,
    .img-thumb-error:hover .overlay{
        display: block;
        background: #1d2124;
        opacity: 0.5;
    }

    .img-thumb-button {
        opacity: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .img-thumb:hover .img-thumb-button,
    .img-thumb-error:hover .img-thumb-button{
        opacity: 1;
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

    .uploading{
        position: absolute;
        width: 200px;
        height: 200px;
        padding: 10px 10px;
    }

</style>