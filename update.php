<?php
if(!Admin::is()) return;
function Rating_Star_update_core() {
    if(Admin::is() && Auth::check() ) {
        $version = Option::get('rating_star_version');
        $version = (empty($version)) ? '3.3.0' : $version;
        if (version_compare(RATING_STAR_VERSION, $version) === 1) {
            $update = new Rating_Star_Update_Version();
            $update->runUpdate($version);
        }
    }
}
add_action('admin_init', 'Rating_Star_update_core');

Class Rating_Star_Update_Version {
    public function runUpdate($DiscountVersion) {
        $listVersion    = ['2.0.0', '3.0.0', '4.0.0'];
        $model          = get_model();
        foreach ($listVersion as $version ) {
            if(version_compare( $version, $DiscountVersion ) == 1) {
                $function = 'update_Version_'.str_replace('.','_',$version);
                if(method_exists($this, $function)) $this->$function($model);
            }
        }
        Option::update('rating_star_version', RATING_STAR_VERSION );
    }
    public function update_Version_2_0_0($model) {
        Rating_Star_Update_Database::Version_2_0_0($model);
    }
    public function update_Version_3_0_0($model) {
        Rating_Star_Update_Database::Version_3_0_0($model);
    }
    public function update_Version_4_0_0($model) {
        Rating_Star_Update_Files::Version_4_0_0($model);
        Rating_Star_Update_Database::Version_4_0_0($model);
    }
}
Class Rating_Star_Update_Database {
    public static function Version_2_0_0($model) {
        if(!$model->db_field_exists('is_read','rating_star')) {
            $model->query("ALTER TABLE `".CLE_PREFIX."rating_star` ADD `is_read` INT NOT NULL DEFAULT '1' AFTER `status`;");
            $model->query("ALTER TABLE `".CLE_PREFIX."rating_star` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `status`;");
            $model->query("UPDATE `".CLE_PREFIX."rating_star` SET `is_read`= '1' WHERE 1;");
        }
    }
    public static function Version_3_0_0($model) {
        if(!$model->db_field_exists('user_id','rating_star')) {
            $model->query("ALTER TABLE `".CLE_PREFIX."rating_star` ADD `user_id` INT NOT NULL DEFAULT '0' AFTER `status`;");
        }
    }
    public static function Version_4_0_0($model) {
        if(!$model->db_field_exists('like','rating_star')) {
            $model->query("ALTER TABLE `".CLE_PREFIX."rating_star` ADD `like` INT NOT NULL DEFAULT '0' AFTER `user_id`;");
        }
        $rating = Option::get('rating_star_setting');
        $rating['template'] = 'template1';
        Option::update('rating_star_setting', $rating);
    }
}
Class Rating_Star_Update_Files {
    public static function Version_4_0_0($model) {
        $path = FCPATH.VIEWPATH.'plugins/'.RATING_STAR_NAME.'/';
        $Files = [
            'admin/rating-star-update.php',
            'admin/views/html-empty.php',
            'template/rating-star-product-form.php',
            'template/rating-star-post-review.php',
            'template/rating-star-post-review-item.php',
        ];
        foreach ($Files as $file) {
            if(file_exists($path.$file)) {
                unlink($path.$file);
            }
        }
    }
}