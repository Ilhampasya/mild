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

class Inspiring
{
    /**
     * Register quotes
     *
     * @var array
     */
    protected static $quotes = [
        'He who is contented is rich. - Laozi',
        'Well begun is half done. - Aristotle',
        'Smile, breathe, and go slowly. - Thich Nhat Hanh',
        'Simplicity is an acquired taste. - Katharine Gerould',
        'Simplicity is the essence of happiness. - Cedric Bledsoe',
        'When there is no desire, all things are at peace. - Laozi',
        'Don’t Let Yesterday Take Up Too Much Of Today. - Will Rogers',
        'It always seems impossible until it is done. - Nelson Mandela',
        'Simplicity is the ultimate sophistication. - Leonardo da Vinci',
        'Very little is needed to make a happy life. - Marcus Antoninus',
        'It is quality rather than quantity that matters. - Lucius Annaeus Seneca',
        'It’s Not Whether You Get Knocked Down, It’s Whether You Get Up. - Vince Lombardi',
        'Service to others is the rent you pay for your room here on earth. - Muhammad Ali',
        'When the whole world is silent, even one voice becomes powerful. - Malala Yousafzai',
        'Genius is one percent inspiration and ninety-nine percent perspiration. - Thomas Edison',
        'Computer science is no more about computers than astronomy is about telescopes. - Edsger Dijkstra',
        'What You Lack In Talent Can Be Made Up With Desire, Hustle And Giving 110% All The Time. - Don Zimmer',
        'Fake It Until You Make It! Act As If You Had All The Confidence You Require Until It Becomes Your Reality. - Brian Tracy',
        'Act only according to that maxim whereby you can, at the same time, will that it should become a universal law. - Immanuel Kant',
        'Entrepreneurs Are Great At Dealing With Uncertainty And Also Very Good At Minimizing Risk. That’s The Classic Entrepreneur. - Mohnish Pabrai',
    ];
    
    /**
     * Get inspiring quote
     *
     * @return string|Collection
     */
    public static function quote()
    {
        return Collection::make(static::$quotes)->random();
    }
}