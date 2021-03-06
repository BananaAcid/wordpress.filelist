<?php
defined('ABSPATH') or die('Can\'t do.');


/**
 * Elementor File List Widget.
 *
 * @since 1.0.0
 */
class BFL_Elementor extends \Elementor\Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve widget name.
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
     * Retrieve widget title.
     *
     * @since 1.0.0
     * @access public
     * @return string Widget title.
     */
    public function get_title()
    {
        return BFL_DISPLAYNAME;
    }

    /**
     * Get widget icon.
     *
     * Retrieve widget icon.
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
     * Retrieve the list of categories the widget belongs to.
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
     * Retrieve the list of keywords the widget belongs to.
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
     * Register widget controls.
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
                'label' => esc_html__('Content', 'BFL'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'path',
            [
                'label' => esc_html__('Path to folder', 'BFL'),
                'description' => esc_html__('The root folder to use', 'BFL'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_html__('./wp-content/uploads', 'BFL'),
                'default' => './wp-content/uploads',
            ]
        );

        $this->add_control(
            'force-download',
            [
                'label' => esc_html__('Links as downloads', 'BFL'),
                'description' => esc_html__('All files will trigger a file download, instead of showing in a popup or new window', 'BFL'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'BFLG' ),
                'label_off' => esc_html__('No', 'BFLG' ),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'important-note',
            [
                'label' => __('<b>Info</b>', 'BFL'),
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __('To protect a file or folder, use a <code>.htaccess</code> file. If there is a <code>.index.html</code> it will be shown.', 'BFL'),
                //'content_classes' => 'your-class',
            ]
        );


        $this->add_control(
            'date-format',
            [
                'label' => esc_html__('Date format', 'BFL'),
                'description' => esc_html__('Format to show the last modified date on files. For the correct display, change the letters arround "Y-m-d H:i:s". To hide it, add the following custom css: `selector .changed, selector .sep.no2 { display: none; }`', 'BFL'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => 'Y-m-d H:i:s',
                'default' => 'Y-m-d H:i:s',
            ]
        );

        $this->add_control(
            'path-prefix',
            [
                'label' => esc_html__('Path prefix', 'BFL'),
                'description' => esc_html__('The URL part to prepend to the links for special use cases, usually just "/"', 'BFL'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => '/',
                'default' => '/',
            ]
        );


        $this->add_control(
            'debug',
            [
                'label' => esc_html__('Show debug', 'BFL'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'BFL'),
                'label_off' => esc_html__('Hide', 'BFL'),
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
     * Get correct m-time for any OS
     * 
     * @see https://www.php.net/manual/de/function.filemtime.php#100692
     * @since 1.0.2
     * @access protected
     */
    function getCorrectMTime($filePath, $format = 'Y-m-d H:i:s')
    {
        $time = filemtime($filePath);

        $isDST = (date('I', $time) == 1);
        $systemDST = (date('I') == 1);

        $adjustment = 0;

        if($isDST == false && $systemDST == true)
            $adjustment = 3600;
    
        else if($isDST == true && $systemDST == false)
            $adjustment = -3600;

        else
            $adjustment = 0;

        return date($format, ($time + $adjustment));
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
            printf(
                '<div class="bfl-filelist error" style="color: red; font-weight: bold;">%1$s %2$s<code>%3$s</code></div>',
                BFL_DISPLAYNAME,
                __(' error: No usable path using', 'BFL'),
                $pathFixed
            );
            return;
        }


        $forceDownload = $settings['force-download'] == 'yes';
        $pathPrefix = $settings['pathPrefix'] ? $settings['pathPrefix'] : '/';
        $debug = $settings['debug'] == 'yes';
        $dateFormat = esc_html__($settings['date-format'] ? $settings['date-format'] : 'Y-m-d H:i:s');
        

        $files = glob($path . '/?*.?*');
        natcasesort($files);

        print "<div class=\"bfl-filelist\">";

        if (file_exists($path . '/.index.html'))
        {
            print "<blockquote class=\"readme\">";
            readfile($path . '/.index.html');
            print "</blockquote>";
        }
        else if ($debug) {
            print "<blockquote class=\"readme debug\">no <code>.index.html</code>: '{$path}/.index.html'</blockquote>";
        }

        print "<ol>";
        foreach($files as $filepath)
        {
            $uri = str_replace(get_home_path(), $pathPrefix, $filepath);
            $name = basename($filepath);
            $size = $this->humanFileSize(filesize($filepath));
            $changed =  $this->getCorrectMTime($filepath, $dateFormat);
            $download = $forceDownload ? ' download ' : '';
            $info = $debug ? "<br><small class=\"debug\"><br/>REAL: {$filepath}</small>" : '';
            $info .= $debug ? "<br><small class=\"debug\"><br/>LINK: {$uri}</small>" : '';

            $line = <<<EOB
                <li><a href="{$uri}" {$download}><span class="name">{$name}</span><span class="sep no1"> &mdash; </span><span class="size">{$size}</span><span class="sep no2"> &mdash; </span><span class="changed">{$changed}</span></a>{$info}</li>
EOB;
            print trim($line);
        }
        print "</ol></div>";
    }

}