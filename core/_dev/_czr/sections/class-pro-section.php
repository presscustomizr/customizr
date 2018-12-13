<?php
/**
 * Pro customizer section.
 * highly based on
 * https://github.com/justintadlock/trt-customizer-pro/blob/master/example-1/section-pro.php
 */
class CZR_Customize_Section_Pro extends WP_Customize_Section {

    /**
     * The type of customize section being rendered.
     *
     * @var    string
     */
    public $type ='czr-customize-section-pro';

    public $pro_subtitle = '';
    public $pro_doc_url = '';

    /**
     * Custom button text to output.
     *
     * @var    string
     */

    public $pro_text = '';
    /**
     *
     * @var    string
     */
    public $pro_url = '';


    /**
     * Add custom parameters to pass to the JS via JSON.
     *
     * @return void
     * @override
     */
    public function json() {
      $json = parent::json();
      $json['pro_subtitle'] = $this->pro_subtitle;
      $json['pro_doc_url']  = $this->pro_doc_url;
      $json['pro_text'] = $this->pro_text;
      $json['pro_url']  = $this->pro_url;
      return $json;
    }

    //overrides the default template
    protected function render_template() { ?>
      <li id="accordion-section-{{ data.id }}" class="accordion-section control-section control-section-{{ data.type }} cannot-expand">
          <h3 style="padding: 10px 2% 18px 14px;display: inline-block;width: 93%;" class="accordion-section-title">
            {{ data.title }}
            <a href="{{ data.pro_doc_url }}" style="font-size: 0.7em;display: block;float: left;position: absolute;bottom: 0px;font-style: italic;color: #555d66;" target="_blank" title="{{ data.pro_subtitle }}">{{ data.pro_subtitle }}</a>
            <# if ( data.pro_text && data.pro_url ) { #>
              <a href="{{ data.pro_url }}" class="button button-secondary alignright" target="_blank" title="{{ data.pro_text }}" style="margin-top:0px">{{ data.pro_text }}</a>
            <# } #>
          </h3>
        </li>
    <?php }
}
?>
