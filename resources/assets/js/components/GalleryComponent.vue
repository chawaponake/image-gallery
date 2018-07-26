<template>
    <div class="row align-content-center text-center gallery">
        <div class="col-md-4" v-for="(media, index) in medias">
            <div class="img-thumb-error" v-if="!media.isImage">
                <div class="overlay"></div>
                <i class="fas fa-exclamation-triangle text-danger fa-7x pt-4"></i>
                <p class="text-danger font-weight-bold">File type not supported. - {{media.name}}</p>
                <div class="img-thumb-button">
                    <button type="button" class="btn btn-danger" @click="deleteMedia(media.id, index)">
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
                        <button type="button" class="btn btn-danger" @click="deleteMedia(media.id, index)">
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
                        <button type="button" class="btn btn-danger" @click="deleteMedia(media.id, index)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props : {
            medias : {
                type : Array,
                required : true,
            }
        },
        methods : {
            showPopup: function(url){
                $('#myModal').modal('show');
                $('#img-popup').attr('src',url);
            },
            deleteMedia : function(id,uuid) {
                this.$emit('delete-media', id,uuid);
            }
        }
    }
</script>

<style lang="scss" scoped>

    .gallery{
        padding-top: 20px;
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

    .uploading{
        position: absolute;
        width: 200px;
        height: 200px;
        padding: 10px 10px;
    }
</style>