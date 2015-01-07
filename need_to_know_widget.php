<?php
/*
 *
 * Need to know widget
 *
 **/

if (class_exists('mmvc')) {
  class need_to_know_controller extends MVC_controller {
    function __construct($args, $instance){
      $this->args = $args;
      $this->instance = $instance;

      parent::__construct();
    }

    function main(){
      $title = apply_filters('widget_title', $this->instance['title']);

      // Get stored story IDs (set in customizer)
      $need_to_know_story1 = get_option('need_to_know_story1');
      $need_to_know_story2 = get_option('need_to_know_story2');
      $need_to_know_story3 = get_option('need_to_know_story3');

      $cquery = array(
        'orderby'         =>'post_date',
        'order'           =>'DESC',
        'post_type'       =>'news',
        'posts_per_page'  => 3,
        'post__in'        => array($need_to_know_story1,$need_to_know_story2,$need_to_know_story3)
      );

      $news = new WP_Query($cquery);

      $widget_data = array(
        'title' => $title,
        'before_widget' => $args['before_widget'],
        'after_widget' => $args['after_widget'],
        'items' => array()
      );

      $news_count = 0;

      while($news->have_posts()){
        $news->the_post();

        $news_count++;

        if($news_count > 5){
          break;
        }

        $widget_data['items'][] = array(
          'offset' => $news_count,
          'id' => $post->ID,
          'url' => get_permalink($ID),
          'title' => get_the_title($post->ID),
          'date' => date("j M Y",strtotime(get_the_date())),
          'excerpt' => get_the_excerpt()
        );
      }

      $this->view('main', $widget_data);

      wp_reset_query();
    }
  }

  class need_to_know_widget extends WP_Widget{
    function need_to_know_widget(){
      parent::WP_Widget(false, 'Need to know', array('description' => '"Need to know" widget'));
    }

    function widget($args, $instance){
      new need_to_know_controller($args, $instance);
    }

    function update($new_instance, $old_instance){
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);

      return $instance;
    }

    function form($instance) {
      $title = esc_attr($instance['title']);
      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /><br><br>
      </p>
      <?php
    }
  }

  add_action('widgets_init', create_function('', 'return register_widget("need_to_know_widget");'));
}

?>
