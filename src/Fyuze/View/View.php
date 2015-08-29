<?php
namespace Fyuze\View;

use InvalidArgumentException;

class View
{
    /**
     * @param $view
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
}
