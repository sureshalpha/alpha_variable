<?php

namespace Kitamula\Kitchen\Traits;

trait Hyperlinkable
{
    /**
    * お気に入りか否かを判定する
    *
    * 引数にテキストが渡された場合、URLを外部リンクにする
    * 第二引数でtargetを指定する
    *
    * @return true|null|string お気に入りフラグ / テキスト
    */
    public function url2hyperlink($property, $target = '_blank', $additionalAttribute = "")
    {
        //
        $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/';
        $replace = "<a href=\"$1\" target=\"{$target}\" {$additionalAttribute} style=\"text-decoration: underline;\">$1</a>";
        $text    = preg_replace( $pattern, $replace, $this->$property );

        // 改行コードをHTMLタグBRに変換
        $text = nl2br($text);
        return $text;
    }
}
