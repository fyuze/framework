<?php
namespace Fyuze\View;

use Exception;
use Fyuze\Error\ErrorHandler;
use InvalidArgumentException;

class View
{
    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param string $view
     * @param array $params
     * @throws InvalidArgumentException
     */
    public function __construct($view, array $params = [])
    {
        $this->params = $params;

        if (!file_exists($view)) {
            throw new InvalidArgumentException(sprintf('The view %s could not be found', $view));
        }

        $this->view = $view;
    }

    /**
     * @return string
     */
    public function render()
    {
        extract($this->params, EXTR_SKIP);

        ob_start();

        include $this->view;

        return ob_get_clean();
    }

    /**
     *
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {

            ob_end_clean();
            ob_start();
            $handler = new ErrorHandler();
            $handler->handle($e);
            return ob_get_clean();
        }
    }
}
