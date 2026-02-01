<?php 
defined("ABSPATH") or die("You are not allowed to access this file.");

class Build {
    
    public function execute() {
        $view_files = glob(ABSPATH . '/views/*.php');
        $data_files = glob(ABSPATH . '/data/*.json');
        
        $data = [];
        foreach ($data_files as $data_file) {
            $data_name = basename($data_file, '.json');
            $data[$data_name] = json_decode(file_get_contents($data_file), true);
        }
        
        foreach ($view_files as $view_file) {
            $view_name = basename($view_file, '.php');
            ob_start();
            require_once $view_file;
            $content = ob_get_clean();
            file_put_contents(ABSPATH . '/public/build/' . $view_name . '.html', $content);
        }
    }
    
}