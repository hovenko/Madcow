<?php


class Sample_Controller_Root extends DF_Web_Controller {
    public static $LOGGER = NULL;


    # Run once for this controller per request
    public function handle_auto($c) {
        error_log("This is Root/auto");
        return true;
    }


    # Chained to the root, see config.yaml
    public function handle_chained($c) {
        error_log("This is Root/chained");
    }


    # The index page, see config.yaml
    public function handle_index($c) {
        error_log("This is Root/index");
        $c->response->content_type('text/plain');
        $c->response->body("Hello world");
        return true;
    }


    # The default page, see config.yaml
    # Fallbacks to this if on other actions match
    public function handle_default($c) {
        error_log("This is Root/default");
        $c->response->status(404);
        $c->response->content_type('text/plain');
        $c->response->body("Nothing found");
        return true;
    }
}

