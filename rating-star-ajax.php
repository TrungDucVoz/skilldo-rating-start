<?php
Class Rating_Star_Ajax {
    static public function reviewLoad($ci, $model) {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if(InputBuilder::post()) {

            $id     =  (int)InputBuilder::Post('object_id');

            $page     =  (int)InputBuilder::Post('page');

            $type   =  InputBuilder::Post('object_type');

            $sort   =  InputBuilder::Post('sort');

            $review_in_page = 3;

            $args = ['where' => ['object_id'=> $id, 'object_type' => $type, 'status' => 'public'], 'params' => []];

            if(!empty($sort)) {
                if($sort == '1-star') $args['where']['star'] = 1;
                if($sort == '2-star') $args['where']['star'] = 2;
                if($sort == '3-star') $args['where']['star'] = 3;
                if($sort == '4-star') $args['where']['star'] = 4;
                if($sort == '5-star') $args['where']['star'] = 5;
            }
            else {
                $args['params']['orderby'] = 'star desc, created desc';
            }

            $count = rating_star::count($args);

            if($count > 0) {

                $config  = array (
                    'current_page'  => ($page != 0) ? $page : 1, // Trang hiện tại
                    'total_rows'    => $count, // Tổng số record
                    'number'		=> $review_in_page,
                    'url'           => '#review{page}',
                );

                $pagination = new paging($config);

                $args['params']['limit'] = $review_in_page;

                $args['params']['start'] = $pagination->getoffset();

                $reviews = rating_star::gets($args);

                $result['review'] = '';

                $reply = true;

                if (rating_star::config('reply') == 'login' && !Auth::check()) $reply = false;

                if (rating_star::config('reply') == 'admin' && (!Auth::check() || !Auth::hasCap('loggin_admin'))) $reply = false;

                foreach ($reviews as $review) {
                    $review->reply = rating_star::gets(['where' => array('parent_id' => $review->id), 'object_type' => 'comment']);
                    ob_start();
                    if($type == 'product') {
                        Plugin::partial(RATING_STAR_NAME, 'rating-star-product-review-item', ['review' => $review, 'reply' => $reply]);
                    }
                    else {
                        Plugin::partial(RATING_STAR_NAME, 'rating-star-post-review-item', ['review' => $review, 'reply' => $reply]);
                    }

                    $result['review'] .= ob_get_contents();
                    ob_end_clean();
                }

                $result['pagination']   = $pagination->html_fontend();

                $result['status'] = 'success';
            }
            else {
                $result['review'] = notice('warning', __('Không có review nào', 'rating_message_empty_review'));

                $result['pagination']   = '';

                $result['status'] = 'success';
            }
        }

        echo json_encode($result);

        return true;
    }
    static public function reviewSave($ci, $model) {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(InputBuilder::post()) {

            $id = (int)InputBuilder::post('object_id');

            $type = InputBuilder::post('object_type');

            if($type == 'product') {
                $object = Product::get($id);
            }
            else {
                $object = Posts::get($id);
            }

            if(!have_posts($object)) {
                $result['message'] = 'Không có đối tượng để đánh giá';
                echo json_encode($result);
                return false;
            }

            $rating = [];

            $rating['object_id']    = $id;

            $rating['object_type']  = $type;

            $rating['star'] = (int)InputBuilder::post('rating');

            $rating['message']  = InputBuilder::post('rating_star_message');

            if(Auth::check()) {
                $user_current = Auth::user();
                $rating['name']           = $user_current->firstname.' '.$user_current->lastname;
                $rating['email']          = $user_current->email;
                $rating['user_id']        = $user_current->id;
            }
            else {
                $rating['name']     = InputBuilder::post('rating_star_name');
                $rating['email']    = InputBuilder::post('rating_star_email');
                if($type == 'post' && empty($rating['name'])) {
                    $rating['name']    = 'guest';
                    $rating['email']   = 'guest_no_isset@empty';
                    $rating['message'] =  'Đanh giá bài viết '.rating_star::starLabel($rating['star']);
                }
            }

            if($type == 'post' && empty($rating['message'])) {
                $rating['message'] =  'Đanh giá bài viết '.rating_star::starLabel($rating['star']);
            }

            if(empty($rating['name'])) {
                $result['message'] = 'Không được để trống tên của bạn.';
                echo json_encode($result);
                return false;
            }

            if(empty($rating['email'])) {
                $result['message'] = 'Không được để trống email của bạn.';
                echo json_encode($result);
                return false;
            }

            if(strlen($rating['message']) < 10) {
                $result['message'] = 'Nội dung đánh giá quá ngắn.';
                echo json_encode($result);
                return false;
            }

            if(!empty($illegal_message)) {
                $illegal_message = explode(',', rating_star::config('illegal_message'));
                if (have_posts($illegal_message)) {
                    foreach ($illegal_message as $illegal) {
                        $illegal = trim($illegal);
                        if (!empty($illegal) && strpos($rating['message'], $illegal) !== false) {
                            $result['message'] = 'Xin lỗi, Nội dung đánh giá có chứa từ không được phép sử dụng.';
                            echo json_encode($result);
                            return false;
                        }
                    }
                }
            }

            if($rating['star'] <= 0 || $rating['star'] > 5) {
                $result['message'] = 'Số sao đánh giá không hợp lệ.';
                echo json_encode($result);
                return false;
            }

            $has_approving  = rating_star::config('has_approving');

            if($has_approving == 1) $rating['status'] = 'hiden';

            $id = rating_star::insert($rating);

            if(!is_skd_error($id)) {

                if(isset($_FILES['attach']) && have_posts($_FILES['attach'])) {

                    if (!file_exists('uploads/rating-star')) {
                        mkdir('uploads/rating-star', 0777, true);
                    }

                    $config['upload_path']		= 'uploads/rating-star';

                    $config['allowed_types']	= 'jpeg|jpg|png';

                    $config['remove_spaces']	= true;

                    $config['detect_mime']      = true;

                    $config['mod_mime_fix']		= true;

                    $config['max_size']     	= '20000';

                    $ci->load->library('upload', $config);

                    $images = [];

                    foreach ($_FILES['attach']['name'] as $key => $image) {

                        if(empty($image)) continue;

                        $extension = FileHandler::extension($image);

                        if($extension == 'unknown') continue;

                        $_FILES['images[]']['name']     = $_FILES['attach']['name'][$key];

                        $_FILES['images[]']['type']     = $_FILES['attach']['type'][$key];

                        $_FILES['images[]']['tmp_name'] = $_FILES['attach']['tmp_name'][$key];

                        $_FILES['images[]']['error']    = $_FILES['attach']['error'][$key];

                        $_FILES['images[]']['size']     = $_FILES['attach']['size'][$key];

                        $fileName = Str::slug(basename($image, '.'.$extension)).'-'.time().'.'.$extension;

                        $config['file_name'] = $fileName;

                        $ci->upload->initialize($config);

                        if ($ci->upload->do_upload('images[]')) {
                            $images[$config['upload_path'].'/'.$fileName] = $fileName;
                        }
                    }

                    if(have_posts($images)) {
                        Metadata::update('rating_star', $id, 'attach', $images);
                    }
                }

                if($has_approving == 0) {

                    $rating_star_product = Metadata::get($type, $rating['object_id'], 'rating_star', true);

                    if(!have_posts($rating_star_product)) {

                        $rating_star_product = ['count' => 0, 'star'  => 0];
                    }

                    $rating_star_product['count'] += 1;

                    $rating_star_product['star']  += $rating['star'];

                    Metadata::update($type, $rating['object_id'], 'rating_star', $rating_star_product);
                }

                $result['status'] = 'success';

                $result['message'] = 'Cám ơn bạn đã gửi đanh giá cho chúng tôi.';
            }
        }

        echo json_encode($result);

        return true;
    }
    static public function reviewReply($ci, $model) {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if(InputBuilder::post()) {

            $data = InputBuilder::post();

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if(have_posts($rating_star)) {

                $rating = [];

                if(Auth::check()) {
                    $user_current = Auth::user();
                    $rating['name']           = $user_current->firstname.' '.$user_current->lastname;
                    $rating['email']          = $user_current->email;
                    $rating['user_id']        = $user_current->id;
                }
                else {
                    $rating['name'] = InputBuilder::post('rating_star_name');

                    if(empty($rating['name'])) {
                        $result['message'] = 'Không được để trống tên của bạn.';
                        echo json_encode($result);
                        return false;
                    }

                    $rating['email'] = InputBuilder::post('rating_star_email');

                    if(empty($rating['email'])) {
                        $result['message'] = 'Không được để trống email của bạn.';
                        echo json_encode($result);
                        return false;
                    }
                }

                $rating['message']     = Str::clear($data['rating_star_message']);

                if(strlen($rating['message']) < 10) {
                    $result['message'] = 'Nội dung đánh giá quá ngắn.';
                    echo json_encode($result);
                    return false;
                }

                if(!empty($illegal_message)) {
                    $illegal_message = explode(',', rating_star::config('illegal_message'));
                    if (have_posts($illegal_message)) {
                        foreach ($illegal_message as $illegal) {
                            $illegal = trim($illegal);
                            if (!empty($illegal) && strpos($rating['message'], $illegal) !== false) {
                                $result['message'] = 'Xin lỗi, Nội dung đánh giá có chứa từ không được phép sử dụng.';
                                echo json_encode($result);
                                return false;
                            }
                        }
                    }
                }

                $rating['object_id']    = $rating_star->object_id;

                $rating['object_type']  = 'comment';

                $rating['status']       = 'public';

                $rating['star']         = 0;

                $rating['parent_id']    = $rating_star->id;

                $id = rating_star::insert($rating);

                if(!is_skd_error($id)) {

                    $result['status'] = 'success';

                    $result['message'] = 'Đăng câu trả lời thành công.';
                }
            }
        }

        echo json_encode($result);

        return true;
    }
    static public function reviewLike($ci, $model) {
        $result['status'] = 'error';
        $result['message'] = 'Lưu dữ liệu không thành công';
        if(InputBuilder::post()) {
            $id = (int)InputBuilder::post('id');
            $rating_star = rating_star::get($id);
            if(have_posts($rating_star)) {
                $rating['id']    = $id;
                $rating['like']  = $rating_star->like + 1;
                $id = rating_star::insert($rating);
                if(!is_skd_error($id)) {
                    $result['status'] = 'success';
                    $result['message'] = 'Cập nhật dữ liệu thành công.';
                }
            }
        }
        echo json_encode($result);
        return true;
    }
}
Ajax::client('Rating_Star_Ajax::reviewLoad');
Ajax::client('Rating_Star_Ajax::reviewSave');
Ajax::client('Rating_Star_Ajax::reviewReply');
Ajax::client('Rating_Star_Ajax::reviewLike');

Class Rating_Star_Admin_Ajax {
    static public function settingSave($ci, $model) {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (InputBuilder::post()) {

            $data = InputBuilder::post('rating_star_setting');
            $rating['product_enable']  = Str::clear($data['product_enable']);
            $rating['post_enable']     = Str::clear($data['post_enable']);
            $rating['has_approving']   = Str::clear($data['has_approving']);
            $rating['color']           = add_magic_quotes($data['color']);
            $rating['illegal_message'] = trim(Str::clear($data['illegal_message']));
            $rating['illegal_message'] = trim($rating['illegal_message'], ',');

            $rating['item_align']       = Str::clear($data['item_align']);
            $rating['item_position']    = (int)Str::clear($data['item_position']);
            $rating['template']         = Str::clear($data['template']);
            $rating['reply']            = Str::clear($data['reply']);
            $rating['auto_enable']      = Str::clear($data['auto_enable']);
            $rating['auto_min_number']  = (int)Str::clear($data['auto_min_number']);
            $rating['auto_max_number']  = (int)Str::clear($data['auto_max_number']);
            $rating['auto_percent_5']   = (int)Str::clear($data['auto_percent_5']);
            $rating['auto_percent_4']   = (int)Str::clear($data['auto_percent_4']);
            $rating['auto_percent_3']   = (int)Str::clear($data['auto_percent_3']);

            if ($rating['auto_min_number'] > $rating['auto_max_number']) {
                $result['message'] = 'Số đánh giá nhỏ nhất tạo ra không thể lớn hơn số đánh giá lớn nhất.';
                echo json_encode($result);
                return false;
            }

            Option::update('rating_star_setting', $rating);

            $result['message'] = 'Cập nhật dữ liệu thành công';

            $result['status'] = 'success';
        }

        echo json_encode($result);
    }
    static public function commentLoad($ci, $model)
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (InputBuilder::post()) {

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if (have_posts($rating_star)) {

                $comments = rating_star::gets(['where' => array('parent_id' => $id), 'object_type' => 'comment']);

                ob_start();

                foreach ($comments as $comment) {
                    ?>
                    <div class="rating-star-comment-item">
                        <div class="rating-star-comment__main" style="position: relative">
                            <div class="rating-star-comment__main_top">
                                <p class="name" itemprop="author"><?php echo $comment->name; ?></p>
                            </div>
                            <div class="rating-star-comment__message">
                                <?php echo $comment->message; ?>
                            </div>
                            <div class="rating-star-comment__action" style="position: absolute; right:0; top:10px;">
                                <button class="btn btn-red js_comment__btn_delete"
                                        data-id="<?php echo $comment->id; ?>"><?php echo admin_button_icon('delete'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                $result['html'] = ob_get_contents();

                ob_end_clean();

                $result['status'] = 'success';

                $result['message'] = 'Load dữ liệu thành công.';
            }
        }

        echo json_encode($result);
    }
    static public function commentSave($ci, $model)
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công';

        if (InputBuilder::post()) {

            $data = InputBuilder::post();

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if (have_posts($rating_star)) {

                $user_current = Auth::user();

                $rating['name'] = Str::clear($data['comment_name']);

                $rating['email'] = $user_current->email;

                $rating['message'] = Str::clear($data['comment']);

                $rating['object_id'] = $rating_star->object_id;

                $rating['object_type'] = 'comment';

                $rating['status'] = 'public';

                $rating['star'] = 0;

                $rating['parent_id'] = $rating_star->id;

                if (empty($rating['message'])) {
                    $result['message'] = 'Không được để trống câu trả lời của bạn.';
                    echo json_encode($result);
                    return false;
                }
                if (strlen($rating['message']) < 10) {
                    $result['message'] = 'Nội dung trả lời quá ngắn.';
                    echo json_encode($result);
                    return false;
                }

                $id = rating_star::insert($rating);

                if (!is_skd_error($id)) {

                    $result['status'] = 'success';

                    $result['message'] = 'Đăng câu trả lời thành công.';
                }
            }
        }

        echo json_encode($result);

        return true;
    }
    static public function commentDelete($ci, $model)
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (InputBuilder::post()) {

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if (have_posts($rating_star)) {

                if (rating_star::delete($id) != 0) {

                    $result['message'] = 'Xóa dữ liệu thành công';

                    $result['status'] = 'success';
                }
            }
        }
        echo json_encode($result);
    }
    static public function statusSave($ci, $model)
    {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if (InputBuilder::post()) {

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if (have_posts($rating_star)) {

                $status = $rating_star->status;

                if ($status == 'public') {

                    $rating_star->status = 'hiden';

                    $result['status'] = '<span class="label label-danger">Ẩn</span>';

                    $result['status_label'] = 'Hiển thị';

                    $rating_star_product = Metadata::get($rating_star->object_type, $rating_star->object_id, 'rating_star', true);

                    if (!have_posts($rating_star_product)) {
                        $rating_star_product = ['count' => 0, 'star' => 0];
                    } else {
                        $rating_star_product['count'] = $rating_star_product['count'] - 1;
                        $rating_star_product['star'] = $rating_star_product['star'] - $rating_star->star;
                    }
                }

                if ($status == 'hiden') {

                    $rating_star->status = 'public';

                    $result['status'] = '<span class="label label-success">Hiển thị</span>';

                    $result['status_label'] = 'Ẩn';

                    $rating_star_product = Metadata::get($rating_star->object_type, $rating_star->object_id, 'rating_star', true);

                    if (!have_posts($rating_star_product)) {
                        $rating_star_product = array('count' => 0, 'star' => 0);
                    } else {

                        $rating_star_product['count'] += 1;

                        $rating_star_product['star'] += $rating_star->star;
                    }
                }

                if (!is_skd_error(rating_star::insert((array)$rating_star))) {

                    update_metadata($rating_star->object_type, $rating_star->object_id, 'rating_star', $rating_star_product);

                    $result['message'] = 'Cập nhật dữ liệu thành công';

                    $result['status'] = 'success';
                }
            }
        }

        echo json_encode($result);
    }
    static public function delete($ci, $model) {

        $result['status']   = 'error';

        $result['message']  = 'Lưu dữ liệu không thành công.';

        if(InputBuilder::post()) {

            $id = (int)InputBuilder::post('id');

            $rating_star = rating_star::get($id);

            if(have_posts($rating_star)) {

                if(rating_star::delete($id) != 0) {
                    $result['message'] 	= 'Cập nhật dữ liệu thành công';
                    $result['status'] 	= 'success';
                }
            }
        }

        echo json_encode($result);

    }
}
Ajax::admin('Rating_Star_Admin_Ajax::settingSave');
Ajax::admin('Rating_Star_Admin_Ajax::commentLoad');
Ajax::admin('Rating_Star_Admin_Ajax::commentSave');
Ajax::admin('Rating_Star_Admin_Ajax::commentDelete');
Ajax::admin('Rating_Star_Admin_Ajax::statusSave');
Ajax::admin('Rating_Star_Admin_Ajax::delete');