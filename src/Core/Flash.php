<?php

namespace App\Core;

class Flash
{
    const FLASH = 'FLASH_MESSAGES';

    const FLASH_ERROR = 'error';
    const FLASH_WARNING = 'warning';
    const FLASH_INFO = 'info';
    const FLASH_SUCCESS = 'success';

    private $defaultOptions = [
        "closeButton" => false,
        "debug" => false,
        "newestOnTop" => false,
        "progressBar" => false,
        "positionClass" => "toast-top-right", // toast-top-right, toast-top-left, toast-bottom-right, toast-bottom-left, toast-top-full-width, toast-bottom-full-width, toast-top-center, toast-bottom-center
        "preventDuplicates" => true,
        "onclick" => null, // JS function to call when the toast is clicked or null
        "showDuration" => "300",
        "hideDuration" => "1000",
        "timeOut" => "5000",
        "extendedTimeOut" => "1000",
        "showEasing" => "swing", // swing, linear
        "hideEasing" => "linear", // swing, linear
        "showMethod" => "fadeIn", // fadeIn, slideDown, shor
        "hideMethod" => "fadeOut" // fadeOut, slideUp, hide
    ];

    public function __construct()
    {
        if (!session()->has(self::FLASH)) {
            session()->set(self::FLASH, []);
        }
    }

    /**
     * Create a flash message
     *
     * @param string $name
     * @param string $message
     * @param string $type
     * @param array $options
     * @return void
     */
    private function create(string $name, string $message, string $type, array $options = []): void
    {
        // remove existing message with the name
        if (session()->has(self::FLASH . $name)) {
            session()->remove(self::FLASH . $name);
        }

        // check if options is valid, check if key in options is present in default options
        foreach ($options as $key => $value) {
            if (!array_key_exists($key, $this->defaultOptions)) {
                throw new \InvalidArgumentException("Invalid option key: $key");
            }
        }

        // Merge default options with user-provided options
        $mergedOptions = array_merge($this->defaultOptions, $options);
        // add the message to the session
        session()->set(self::FLASH, [
            $name => ['message' => $message, 'type' => $type, 'options' => $mergedOptions]
        ]);
    }

    /**
     * Format a flash message for Toastr
     *
     * @param array $flash_message
     * @return string
     */
    private function format(array $flash_message): string
    {
        $options = !empty($flash_message['options']) ? json_encode($flash_message['options']) : '{}';
        return sprintf(
            '<script>toastr.options = %s; toastr.%s("%s");</script>',
            $options,
            $flash_message['type'],
            addslashes($flash_message['message'])
        );
    }

    /**
     * Display a flash message
     *
     * @param string $name
     * @return void
     */
    private function display(string $name): void
    {
        if (!session()->has(self::FLASH . $name)) {
            return;
        }

        // get message from the session
        $flash_message = session()->get(self::FLASH, $name);

        // delete the flash message
        session()->remove(self::FLASH . $name);

        // display the flash message
        echo $this->format($flash_message);
    }

    /**
     * Display all flash messages
     *
     * @return void
     */
    private function displayAll(): void
    {
        if (!session()->has(self::FLASH)) {
            return;
        }

        // get flash messages
        $flash_messages = session()->get(self::FLASH);

        // remove all the flash messages
        session()->remove(self::FLASH);

        // show all flash messages
        foreach ($flash_messages as $flash_message) {
            echo $this->format($flash_message);
        }
    }

    /**
     * Flash a message using toastr
     *
     * @param string $name
     * @param string $message
     * @param string $type (error, warning, info, success)
     * @param array $options
     * @return void
     */
    public function flash($name, $message = '', string $type = '', array $options = []): void
    {
        if ($name !== '' && $message !== '' && $type !== '') {
            // create a flash message
            $this->create($name, $message, $type, $options);
        } elseif ($name !== '' && $message === '' && $type === '') {
            // display a flash message
            $this->display($name);
        } elseif ($name === '' && $message === '' && $type === '') {
            // display all flash messages
            $this->displayAll();
        }
    }
}
