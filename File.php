<?php

class File {
    public $path;
    public $name;
    public $type;

    public $tags = [];

    public function __construct($path) {
        $this->path = $path;
        $this->name = basename($this->path);
        $this->type = pathinfo($this->path, PATHINFO_EXTENSION);

        try {
            $this->getMavericksTags();
        } catch(Exception $e) {
            // No tags found, try to get label

            try {
                $this->getFinderLabel();
            } catch(Exception $e) {

            }
        }

    }

    protected function getMavericksTags() {
        $command = 'xattr -p com.apple.metadata:_kMDItemUserTags ' . $this->path;
        $shell = shell_exec($command);

        if($shell === null) {
            throw new Exception('No tags found');
        }

        $tagColors = [
            1 => 'grey',
            2 => 'green',
            3 => 'purple',
            4 => 'blue',
            5 => 'yellow',
            6 => 'red',
            7 => 'orange'
        ];

        $plist = new \CFPropertyList\CFPropertyList();
        $shell = preg_replace('/\\s/', '', $shell);
        $shell = File::hex2str($shell);
        $plist->parse($shell);
        $colors = $plist->toArray();
        foreach($colors as $color) {
            $color = explode("\n", $color);
            if(isset($color[1])) {
                $this->tags[] = $tagColors[$color[1]];
            }
        }
    }

    protected function getFinderLabel() {
        $command = 'xattr -p com.apple.FinderInfo ' . $this->path;
        $shell = shell_exec($command);

        if($shell === null) {
            throw new Exception('No label found');
        }

        $labelColors = [
            2 => 'grey',
            4 => 'green',
            6 => 'purple',
            8 => 'blue',
            10 => 'yellow',
            12 => 'red',
            14 => 'orange',
        ];

        $colorHex = substr($shell, 28, 1);

        $color = hexdec($colorHex);
        $this->tags[] = $labelColors[$color];
    }

    protected static function hex2str($hex) {
        $str = '';
        for($i = 0; $i < strlen($hex); $i += 2) {
            $str .= chr(hexdec(substr($hex, $i, 2)));
        }
        return $str;
    }
}
