<div class="rating-star-form">
    <div class="rating-star-review">
        <div class="rsr__left">
            <div class="review__info">
                <span class="number"><?php echo $star;?>.0</span>
                <span class="star">
                    <?php for( $i = 0; $i < $star; $i++ ) {?>
                        <i class="fal fa-star" aria-hidden="true" style="color:var(--star-color); font-weight: bold;"></i>&nbsp;
                    <?php } ?>
                    <?php for( $i = 0; $i < (5 - $star); $i++ ) {?>
                        <i class="fas fa-star" aria-hidden="true" style="color:#ccc;"></i>&nbsp;
                    <?php } ?>
                </span>
                <span class="text"><?php echo $count;?> <?php echo __('đánh giá', 'rating_rate');?></span>
            </div>
            <?php for ($i = 5; $i > 0 ; $i--) { ?>
                <?php
                if($count == 0) {
                    $count_item_star = 0; $percent = 0;
                }
                else {
                    $count_item_star = rating_star::count(['where' => ['star' => $i, 'object_type' => 'product', 'object_id' => $object->id]]);
                    $percent = ceil($count_item_star/$count*100);
                }
                ?>
                <div class="r">
                    <span class="t">
                        <?php for( $num = 0; $num < $i; $num++ ) {?><i class="fal fa-star" style="color:var(--star-color); font-weight: bold;"></i><?php } ?>
                        <?php for( $num = 0; $num < (5-$i); $num++ ) {?><i class="fal fa-star" style="color:var(--star-color);"></i><?php } ?>
                    </span>
                    <div class="bgb"> <div class="bgb-in" style="width: <?php echo $percent;?>%"></div> </div>
                    <span class="c"><strong><b><?php echo $percent;?>%</b> | <?php echo $count_item_star;?></strong></span>
                </div>
            <?php } ?>
        </div>
        <div class="rsr__right">
            <div class="row">
                <div class="form-group col-md-12">
                    <p class="text-center"><span class="text">Có <?php echo $count;?> <?php echo __('đánh giá', 'rating_rate');?></span></p>
                    <button type="button" class="btn btn-theme btn-effect-default btn-block js_rating_star_btn__review"><?php echo __('Gửi đánh giá của bạn');?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal micromodal-slide rating_star_modal__review" id="js_rating_star_modal__review" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
                <h2 class="modal__title">ĐÁNH GIÁ SẢN PHẨM</h2>
                <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <div class="modal__content js_rating_star_modal__review">
                <div class="rating-reviews__success">
                    <div class="text-center">
                        <?php Template::img(Url::base(RATING_STAR_PATH.'/assets/images/success.png'));?>
                        <p>Cám ơn bạn đã gửi đánh giá cho sản phẩm này! Đánh giá của bạn sẻ giúp chúng tôi cải thiện chất lượng dịch vụ hơn nữa.</p>
                    </div>
                </div>
                <form class="review-form" method="post" enctype="multipart/form-data" id="rating-reviews__form" autocomplete="off">
                    <input name="object_id" type="hidden" value="<?php echo $object->id;?>">
                    <input name="object_type" type="hidden" value="product">
                    <div class="rating">
                        <label class="selected">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 32 32">
                                <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                            <input type="radio" name="rating" value="5" title="5 stars" checked> 5
                        </label>
                        <label>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 32 32">
                                <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                            <input type="radio" name="rating" value="4" title="4 stars"> 4
                        </label>
                        <label>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 32 32">
                                <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                            <input type="radio" name="rating" value="3" title="3 stars"> 3
                        </label>
                        <label>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 32 32">
                                <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                            <input type="radio" name="rating" value="2" title="2 stars"> 2
                        </label>
                        <label>
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 32 32">
                                <path fill="none" fill-rule="evenodd" stroke="#FFB500" stroke-width="1.5" d="M16 1.695l-4.204 8.518-9.401 1.366 6.802 6.631-1.605 9.363L16 23.153l8.408 4.42-1.605-9.363 6.802-6.63-9.4-1.367L16 1.695z"></path>
                            </svg>
                            <input type="radio" name="rating" value="1" title="1 star"> 1
                        </label>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <textarea name="rating_star_message" class="form-control" rows="5" required placeholder="<?php echo __('Hãy chia sẻ những điều bạn thích về sản phẩm này nhé', 'rating_placeholder_message');?>"></textarea>
                        </div>
                        <?php if(!Auth::check()) {?>
                        <div class="form-group col-md-6">
                            <input name="rating_star_name" value="<?php echo $form['name'];?>" type="text" class="form-control" placeholder="<?php echo __('Họ tên của bạn', 'rating_placeholder_name');?>" required <?php if(Auth::check()) echo 'readonly';?>>
                        </div>
                        <div class="form-group col-md-6">
                            <input name="rating_star_email" value="<?php echo $form['email'];?>" type="email" class="form-control" placeholder="<?php echo __('Email của bạn', 'rating_placeholder_email');?>" required <?php if(Auth::check()) echo 'readonly';?>>
                        </div>
                        <?php } ?>
                        <div class="form-group col-md-12 review-attach-box">
                            <div class="review-attach-text"><span class="btn-attach js_rating_star_insert_attach"><?php echo __('Gửi ảnh thực tế', 'rating_upload_image');?></span></div>
                            <div class="review-attach-list">
                                <?php for($i = 1; $i <= 5; $i++) {?>
                                    <div class="uploader">
                                        <div class="input-wrapper input-wrapper--button">
                                            <div class="input-group">
                                                <input type="file" name="attach[]" class="input__upload" id="attach_file_<?php echo $i;?>">
                                                <label class="uploader-review" for="attach_file_<?php echo $i;?>"><i class="fal fa-plus"></i></label>
                                                <div class="remove-file"><i class="fal fa-times"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <div class="col-md-12">
                                    <p style="text-align: center; font-size: 12px; margin-top: 10px;"><?php echo __('Chỉ chấp nhận JPEG, JPG, PNG. Dung lượng không quá 2Mb mỗi hình', 'rating_upload_image_validation');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <button type="submit" class="btn btn-theme btn-effect-default btn-block"><?php echo __('Gửi');?></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php echo Admin::loading();?>
        </div>
    </div>
</div>

<style>
    .rating-star-form {
        margin: 10px auto 0 auto;
        background-color:#fff;
        border: solid 1px #eee;
        border-radius: 10px;
    }
    .rating-star-form .rating-star-review {
        box-sizing: border-box;
        overflow:hidden;
        background-color: #F5F5F5;
    }
    .rating-star-form .rating-star-review .rsr__left {
        width: 50%;
        float: left;
        border-right: solid 1px #eee;
        padding: 20px;
        text-align: left;
        box-sizing: border-box;
    }
    .rating-star-form .rating-star-review .review__info {
        margin: 0 0 10px 0;
    }
    .rating-star-form .rating-star-review .review__info span.number {
        font-size: 50px;
        vertical-align: middle;
        font-weight: 700;
        text-align: center;
        margin: 0 10px 0 0;
        line-height: 1;
    }
    .rating-star-form .rating-star-review .review__info span.star {
        overflow: hidden; font-size: 18px;
    }
    .rating-star-form .rating-star-review .r {
        text-align: left; margin: 0px 0;
    }
    .rating-star-form .rating-star-review span.t {
        display: inline-block;
        color: #333;
    }
    .rating-star-form .rating-star-review span.t i {
        font-size: 12px; margin-right: 3px;
        display: inline-block;
    }
    .rating-star-form .rating-star-review .bgb {
        width: 40%;
        background-color: #e9e9e9;
        height: 10px;
        display: inline-block;
        margin: 0 10px;
        border-radius: 5px;
    }
    .rating-star-form .rating-star-review .bgb .bgb-in {
        background-color: #f25800;
        background-image: linear-gradient(90deg,#ff7d26 0%,#f25800 97%);
        height: 10px;
        border-radius: 5px 0 0 5px;
        max-width: 100%;
    }
    .rating-star-form .rating-star-review span.c {
        font-size: 14px; font-weight: bold;
        display: inline-block;
    }
    .rating-star-form .rating-star-review .rsr__right {
        font-size: 13px;
        overflow: hidden;
        box-sizing: border-box;
        padding: 20px;
        width: 50%;
        float: left;
    }

    @media(max-width:768px) {
        .rating-star-form .rating-star-review { height:auto; }
        .rating-star-form .rating-star-review .rsr__left { width:100%; }
        .rating-star-form .rating-star-review .rsr__right {
            width:100%;padding-top: 20px;
            padding-bottom: 20px;
            height: auto;
        }
        .rating-star-form .rating-star-review .rsr__right .bgb { width:46%;}
        .rating-star-form .rating-star-review .rsr__right a { margin-top:0px;}
    }

    .rating_star_modal__review .rating {
        text-align: center;
        unicode-bidi: bidi-override;
        direction: rtl;
        margin-bottom: 30px;
    }
    .rating_star_modal__review .rating input {
        position: absolute;
        left: -999999px;
    }
    .rating_star_modal__review .rating label {
        display: inline-block;
        font-size: 0;
        cursor: pointer;
    }

    .rating_star_modal__review .rating > label:hover svg path,
    .rating_star_modal__review .rating > label:hover ~ label svg path,
    .rating_star_modal__review .rating > label.selected svg path,
    .rating_star_modal__review .rating > label.selected ~ label svg path {
        fill: #FDD835 !important;
    }
    .rating-reviews__success { display: none; padding:50px 10px}
    .rating-reviews__success img { width: 50px; margin-bottom: 20px; }

    .review-attach-box span.btn-attach {
        color: var(--theme-color);
        font-weight: 700;
        padding: 5px 0 5px 32px;
        display: block;
        background: transparent url('<?php echo RATING_STAR_PATH.'/assets/images/icon-image.png';?>') no-repeat left center;
        background-size: 25px auto;
        -moz-background-size: 25px auto;
        -webkit-background-size: 25px auto;
        cursor: pointer;
    }
    .review-attach-list { overflow: hidden; display: none;}
    .review-attach-list .uploader {
        float: left;
        margin-right: 10px;
        margin-top: 10px;
        position: relative;
    }
    .review-attach-list .uploader input[type=file] { display: none;}
    .review-attach-list .uploader-review {
        cursor: pointer;
        height: 80px; width: 80px;
        border: 1px dashed #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    .review-attach-list .uploader.insert-file .remove-file {
        position: absolute; width: 20px; height: 20px; background-color: #000; color:#fff;
        text-align: center; line-height: 20px; font-size: 10px;
        border-radius: 50%; top:-10px;right: 0; cursor: pointer;
        display: block;
    }
    .review-attach-list .uploader .remove-file { display: none;}
</style>

<script type="text/javascript" defer>
    $(function(){

        $('.js_rating_star_btn__review').click(function() {
            MicroModal.show('js_rating_star_modal__review');
        });

        let rating_star_form = $('#rating-reviews__form');

        let allowed_extensions = ["jpeg","png","jpg"];

        $(".review-attach-list input[type=file]").change(function() {
            let validate = false;
            let img = $(this).val();
            let extension = img.split('.').pop().toLowerCase();
            if(img.length === 0) return false;
            for(let i = 0; i <= allowed_extensions.length; i++) {
                if(allowed_extensions[i] === extension) {
                    validate = true;
                    break;
                }
            }
            if(validate === false) {
                show_message('Ảnh không đúng định dạng.','error');
                return false;
            }
            let size = $(this)[0].files[0].size;
            size = size / 1024 / 1024;
            if(size > 2) {
                show_message('Ảnh của bạn lớn hơn kích thước cho phép.','error');
                return false;
            }
            readURL($(this), this);
        });

        $('.js_rating_star_insert_attach').click(function () {
            $('.review-attach-list').toggle();
        });

        $('.review-attach-list .remove-file').click(function () {
            let box = $(this).closest('.uploader');
            box.find('.uploader-review').html('<i class="fal fa-plus"></i>');
            box.find('input.input__upload').val('');
            box.removeClass('insert-file');
        });

        function readURL(obj, input) {
            let number = obj.attr('id');
            if (input.files && input.files[0]) {
                let fileUpload = input.files[0];
                let reader = new FileReader();
                reader.onload = function (e) {
                    $('.uploader-review[for="'+number+'"]').html('<img src="'+e.target.result+'">');
                    $('.uploader-review[for="'+number+'"]').closest('.uploader').addClass('insert-file');
                };
                reader.readAsDataURL(fileUpload);
            }
        }

        $('.rating input').change(function () {
            let $radio = $(this);
            $('.rating .selected').removeClass('selected');
            $radio.closest('label').addClass('selected');
        });

        rating_star_form.submit(function(){

            let form = $(this);

            let file_data = $(this).find('input.input__upload').prop('files')[0];

            let form_data = new FormData($(this)[0]);

            let loading = form.find('.loading');

            form_data.append('file', file_data);

            form_data.append('data', $(this).serializeJSON());

            form_data.append('action', 'Rating_Star_Ajax::reviewSave');

            form_data.append('csrf_test_name', encodeURIComponent(getCookie('csrf_cookie_name')));

            loading.show();

            $.ajax({
                url: ajax, // gửi đến file upload.php
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                beforeSend: function() {},
                success: function (response) {
                    loading.hide();
                    if(response.status === 'success') {
                        rating_star_form.hide();
                        rating_star_form.trigger('reset');
                        $('.rating-reviews__success').show();
                    }
                    else {
                        show_message(response.message, response.status);
                    }
                    form.trigger('reset');
                }
            });

            return false;
        });
    })
</script>