<?php
defined('ABSPATH') or die('Can\'t do.');


/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class BFL_Elementor extends \Elementor\Widget_Base
{

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
    {
		return 'bfl';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
    {
		return esc_html__( 'Filelist', 'BFL' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
    {
		return 'eicon-code';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url()
    {
		return 'https://virally.de/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories()
    {
		return [ 'general' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords()
    {
		return [ 'bfl', 'url', 'link' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
    {
        $this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'BFL' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'path',
			[
				'label' => esc_html__('Path to folder', 'BFL' ),
                'description' => esc_html__('The root folder to use', 'BFL' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'text',
				'placeholder' => esc_html__( './wp-content/uploads', 'BFL' ),
                'default' => './wp-content/uploads',
			]
		);

        $this->add_control(
			'forceDownload',
			[
				'label' => esc_html__( 'Links as downloads', 'BFL' ),
                'description' => esc_html__( 'All files will trigger a file download, instead of showing in a popup or new window', 'BFL' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'BFLG' ),
				'label_off' => esc_html__( 'No', 'BFLG' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
        );

        $this->add_control(
			'important_note',
			[
				'label' => __( '<b>Info</b>', 'BFL' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => __('To protect a file or folder, use an <code>.htaccess</code> file. If there is a <code>.index.html</code>  it will be shown.', 'BFL' ),
				//'content_classes' => 'your-class',
			]
		);

        $this->add_control(
			'debug',
			[
				'label' => esc_html__( 'Show debug', 'BFL' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'BFL' ),
				'label_off' => esc_html__( 'Hide', 'BFL' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

        $this->end_controls_section();
	}

	/**
	 * Transform bytes to something readable
	 *
	 * @since 1.0.0
	 * @access protected
	 */
    protected function humanFileSize($size,$unit="") {
        if( (!$unit && $size >= 1<<30) || $unit == " GB")
            return number_format($size/(1<<30),2)." GB";
        if( (!$unit && $size >= 1<<20) || $unit == " MB")
            return number_format($size/(1<<20),2)." MB";
        if( (!$unit && $size >= 1<<10) || $unit == " KB")
            return number_format($size/(1<<10),2)." KB";
        return number_format($size)." Bytes";
    }

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
    {
		$settings = $this->get_settings_for_display();
        $pathFixed = (substr($settings['path'], 0, 1) !== '/') ? get_home_path() . $settings['path'] : $settings['path'];
		$path = realpath($pathFixed);

        if (empty($path))
        {
            print "<div class=\"filelist error\" style=\"color: red; font-weight: bold;\">Filelist error: No usable path using <code>{$pathFixed}</code></div>";
            return;
        }


        $forceDownload = $settings['forceDownload'] == 'yes';
        $debug = $settings['debug'] == 'yes';
        

        $files = glob($path . '/?*.?*');
        //$files = scandir($path);
        natcasesort($files);

        print "<div class=\"filelist\">";

        if (file_exists($path . '/.index.html'))
        {
            print "<blockquote class=\"readme\">";
            readfile($path . '/.index.html');
            print "</blockquote>";
        }
        else if ($debug) {
            print "<blockquote class=\"debug readme\">no <code>.index.html</code>: '{$path}/.index.html'</blockquote>";
        }

        print "<ol>";
        foreach($files as $filepath)
        {
            $uri = str_replace(get_home_path(), '', $filepath);
            $name = basename($filepath);
            $size = $this->humanFileSize(filesize($filepath));
            $download = $forceDownload ? ' download ' : '';
            $info = $debug ? "<small class=\"debug\"><br/>{$filepath}</small>" : '';

            $line = <<<EOB
                <li><a href="{$uri}" {$download}><span class="name">{$name}</span><span class="sep"> &mdash; </span><span class="size">{$size}</span></a>{$info}</li>
EOB;
            print trim($line);
        }
        print "</ol></div>";
    }

}