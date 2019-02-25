<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Supports;

use Countable;

class ViewErrorBag implements Countable
{
    /**
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * ViewErrorBag constructor.
     * @param MessageBag|null $messageBag
     */
    public function __construct($messageBag = null)
    {
        $this->messageBag = $messageBag ?: new MessageBag;
    }

    public function all()
    {
        $items = [];
        foreach ($this->messageBag->getItems() as $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $items[] = $value;
                }
            } else {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->messageBag->count();
    }

    /**
     * @return MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->messageBag->$name(...$arguments);
    }
}