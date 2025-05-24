<?php

if (!function_exists('app_name')) {
    /**
     * Get product avatar src by product images save in database
     * @param string $prodImages
     * @return mixed|string
     */
    function get_product_avatar_src_by_proImages(string $prodImages): mixed
    {
        $prodImages = json_decode($prodImages);
        $avatarSrc = '#';
        if (!empty($prodImages[0])) {
            $avatar = $prodImages[0];
            str_contains($avatar, 'https://') ?
                $avatarSrc = $avatar :
                $avatarSrc = asset('public/' . \App\Models\Product::PUBLIC_PROD_IMAGE_FOLDER . '/' . $avatar);
        }

        return $avatarSrc;
    }
}

?>
