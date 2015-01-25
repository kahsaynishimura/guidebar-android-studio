<?php

/**
 * Component for working with mPDF class.
 * mPDF has to be in the vendors directory.
 */
class MpdfComponent extends Component {

    /**
     * Instance of mPDF class
     * @var object
     */
    protected $pdf;

    /**
     * Default values for mPDF constructor  
     * @var array
     */
    protected $_configuration = array(
        // mode: 'c' for core fonts only, 'utf8-s' for subset etc.
        'mode' => 'utf8-s',
        // page format: 'A0' - 'A10', if suffixed with '-L', force landscape
        'format' => 'A4',
        // default font size in points (pt)
        'font_size' => 0,
        // default font
        'font' => NULL,
        // page margins in mm
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 5,
        'margin_bottom' => 5,
        'margin_header' => 9,
        'margin_footer' => 9
    );

    /**
     * Flag set to true if mPDF was initialized
     * @var bool
     */
    protected $_init = false;

    /**
     * Name of the file on the output
     * @var string
     */
    protected $_filename = NULL;

    /**
     * Destination - posible values are I, D, F, S
     * @var string
     */
    protected $_output = 'I';

    /**
     * Initialize 
     * Add vendor and define mPDF class.
     */
    public function init($configuration = array()) {
        // mPDF class has many notices - suppress them
        error_reporting(0);
        // import mPDF

        App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'mpdf')));
        //App::uses('mPDF', 'Vendor');
        App::import('Vendor', 'mpdf/mpdf');
        if (!class_exists('mPDF'))
            throw new CakeException('Vendor class mPDF not found!');

        // override default values
        $c = array();
        foreach ($this->_configuration as $key => $val)
            $c[$key] = array_key_exists($key, $configuration) ? $configuration[$key] : $val;
        // initialize
        $this->pdf = new mPDF($c['mode'], $c['format'], $c['font_size'], $c['font'], $c['margin_left'], $c['margin_right'], $c['margin_top'], $c['margin_bottom'], $c['margin_header'], $c['margin_footer']);
        $this->_init = true;
    }

    /**
     * Set filename of the output file 
     */
    public function setFilename($filename) {
        $this->_filename = (string) $filename;
    }

    /**
     * Set destination of the output 
     */
    public function setOutput($output) {
        if (in_array($output, array('I', 'D', 'F', 'S')))
            $this->_output = $output;
    }

    /**
     * Shutdown of the component
     * View is rendered but not yet sent to browser.
     */
    public function shutdown(Controller $controller) {
        if ($this->_init) {

            App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'mpdf')));
            // LOAD a stylesheets
            $stylesheet = file_get_contents(WWW_ROOT . DS . 'css' . DS . 'normalize.css');
            $this->pdf->WriteHTML($stylesheet, 1);
            $stylesheet = file_get_contents(WWW_ROOT . DS . 'css' . DS . 'bootstrap.min.css');
            $this->pdf->WriteHTML($stylesheet, 1);
            $stylesheet = file_get_contents(WWW_ROOT . DS . 'css' . DS . 'generic.css');
            $this->pdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
            $stylesheet = file_get_contents(WWW_ROOT . DS . 'css' . DS . 'mpdf.css');
            $this->pdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text

//            $this->pdf->SetDisplayMode('fullpage');
//            $this->pdf->SetColumns(3, 'J');
            $this->pdf->WriteHTML((string) $controller->response);
            $this->pdf->Output($this->_filename, $this->_output);
            exit;
        }
    }

    /**
     * Passing method calls and variable setting to mPDF library.
     */
    public function __set($name, $value) {
        $this->pdf->$name = $value;
    }

    public function __get($name) {
        return $this->pdf->$name;
    }

    public function __isset($name) {
        return isset($this->pdf->$name);
    }

    public function __unset($name) {
        unset($this->pdf->$name);
    }

    public function __call($name, $arguments) {
        call_user_func_array(array($this->pdf, $name), $arguments);
    }

}
