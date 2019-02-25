<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Views;

use Throwable;
use InvalidArgumentException;
use Mild\Supports\Traits\Macroable;

class View
{
    use Macroable;
    /**
     * @var array
     */
    protected $paths = [
      'hints' => [],
      'original' => '',
      'compiled' => '',
    ];

    /**
     * @var array
     */
    protected $footer = [];
    /**
     * @var array
     */
    protected $shared = [];
    /**
     * @var array
     */
    protected $sections = [];
    /**
     * @var bool
     */
    protected $switch = true;

    /**
     * View constructor.
     * @param array $paths
     */
    public function __construct($paths)
    {
        if (isset($paths['hints'])) {
            $this->paths['hints'] = $paths['hints'];
        }
        if (isset($paths['original'])) {
            $this->paths['original'] = $paths['original'];
        }
        if (isset($paths['compiled'])) {
            $this->paths['compiled'] = $paths['compiled'];
        }
    }

    /**
     * @param $__file
     * @param array $__data
     * @return mixed
     * @throws Throwable
     */
    public function render($__file, array $__data = [])
    {
        if (strpos($__file, '::') !== false) {
            $segments = explode('::', $__file);
            if (isset($this->paths['hints'][$segments[0]])) {
                $__file = $this->paths['hints'][$segments[0]].'/'.$segments[1];
            }
        } else {
            $__file = $this->paths['original'].'/'.$__file;
        }
        if (!file_exists($__file)) {
            throw new InvalidArgumentException("File [$__file] does not exist.");
        }
        $content = trim($this->statementsCompile($this->echosCompile($this->tagCompile(preg_replace('/{{--(.*?)--}}/s', '', file_get_contents($__file)))))."\n".implode("\n", $this->footer));
        $this->footer = [];
        $__file = $this->paths['compiled'].'/'.sha1($__file).'.php';
        file_put_contents($__file, $content);
        foreach ($this->shared as $key => $share) {
            $__data[$key] = $share;
        }
        $__level = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);
        try {
            include $__file;
        } catch (Throwable $e) {
            while (ob_get_level() > $__level) {
                ob_end_clean();
            }
            throw $e;
        }
        return ob_get_clean();
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param $__file
     * @param array $__data
     * @throws Throwable
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function renderResponse($response, $__file, $__data = [])
    {
        $response->getBody()->write($this->render($__file, $__data));
        return $response;
    }

    /**
     * @param $name
     * @param string $content
     * @return string
     */
    protected function yieldContent($name, $content = '')
    {
        if (!isset($this->sections[$name])) {
            return $content;
        }
        return $this->sections[$name];
    }

    /**
     * @param $name
     * @param string $content
     * @return void
     */
    protected function startSection($name, $content = '')
    {
        ob_start();
        $this->sections[$name] = $content;
    }

    /**
     * @return void
     */
    protected function endSection()
    {
        $keys = array_keys($this->sections);
        $keys = array_pop($keys);
        $content = $this->sections[$keys];
        if ($content) {
            $this->sections[$keys] = $content.ob_get_clean();
        } else {
            $this->sections[$keys] = ob_get_clean();
        }
    }

    /**
     * Hint a alias path
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function hint($key, $value)
    {
        $this->paths['hints'][$key] = $value;
        return $this;
    }

    /**
     * Share a variable to view
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function share($name, $value)
    {
        $this->shared[$name] = $value;
        return $this;
    }

    /**
     * Alias method on setMacro method
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function extend($key, $value)
    {
        static::setMacro($key, $value);
        return $this;
    }

    /**
     * @return array
     */
    public function getShared()
    {
        return $this->shared;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @return array
     */
    public function getHintsPath()
    {
        return $this->paths['hints'];
    }

    /**
     * @return mixed
     */
    public function getOriginalPath()
    {
        return $this->paths['original'];
    }

    /**
     * @return mixed
     */
    public function getCompiledPath()
    {
        return $this->paths['compiled'];
    }

    /**
     * @param $content
     * @return string|string[]|null
     */
    protected function tagCompile($content)
    {
        return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return '<?php'.$matches[1].'?>';
        }, $content);
    }

    /**
     * @param $content
     * @return string|string[]|null
     */
    protected function echosCompile($content)
    {
        return $this->echoRegularCompile($this->echoEscapedCompile($this->echoRawCompile($content)));
    }

    /**
     * @param $content
     * @return string|string[]|null
     */
    protected function echoRawCompile($content)
    {
        return preg_replace_callback('/(@)?{!!\s*(.+?)\s*!!}(\r?\n)?/s', function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            return $matches[1] ? $matches[0] : '<?php echo '.$matches[2].'; ?>'.$whitespace.'';
        }, $content);
    }

    /**
     * @param string $content
     * @return string|string[]|null
     */
    protected function echoRegularCompile($content)
    {
        return preg_replace_callback('/(@)?{{\s*(.+?)\s*}}(\r?\n)?/s', function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            return $matches[1] ? $matches[0] : '<?php echo e('.$matches[2].'); ?>'.$whitespace.'';
        }, $content);
    }

    /**
     * @param $content
     * @return string|string[]|null
     */
    protected function echoEscapedCompile($content)
    {
        return preg_replace_callback('/(@)?{{{\s*(.+?)\s*}}}(\r?\n)?/s', function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            return $matches[1] ? $matches[0] : '<?php echo e('.$matches[2].'); ?>'.$whitespace.'';
        }, $content);
    }

    /**
     * @param $content
     * @return string|string[]|null
     */
    protected function statementsCompile($content)
    {
        return preg_replace_callback('/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', function ($match) {
            if (strpos($match[1], '@') !== false) {
                $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
            }
            if (isset($match[3])) {
                $expression = $match[3];
            } else {
                $expression = null;
            }
            if (static::hasMacro($match[1])) {
                $match[0] = $this->{$match[1]}($this->stripParentheses($expression));
            } elseif (method_exists($this, $method = strtolower($match[1]) .'Compile')) {
                $match[0] = $this->$method($expression);
            }
            return !empty($expression) ? $match[0] : $match[0].$match[2];
        }, $content);
    }

    /**
     * @param $expression
     * @return string
     */
    protected function ifCompile($expression)
    {
        return '<?php if'.$expression.': ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function elseifCompile($expression)
    {
        return '<?php elseif'.$expression.': ?>';
    }

    /**
     * @return string
     */
    protected function elseCompile()
    {
        return '<?php else: ?>';
    }

    /**
     * @return string
     */
    protected function endifCompile()
    {
        return '<?php endif; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function switchCompile($expression)
    {
        $this->switch = true;
        return '<?php switch'.$expression.':';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function caseCompile($expression)
    {
        if ($this->switch) {
            $this->switch = false;
            return 'case '.$expression.': ?>';
        }
        return '<?php case '.$expression.': ?>';
    }

    /**
     * @return string
     */
    protected function endswitchCompile()
    {
        return '<?php endswitch; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function foreachCompile($expression)
    {
        return '<?php foreach'.$expression.': ?>';
    }

    /**
     * @return string
     */
    protected function endforeachCompile()
    {
        return '<?php endforeach; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function forCompile($expression)
    {
        return '<?php for'.$expression.': ?>';
    }

    /**
     * @return string
     */
    protected function endforCompile()
    {
        return '<?php endfor; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function unsetCompile($expression)
    {
        return '<?php unset'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function configCompile($expression)
    {
        return '<?php echo config'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function phpCompile($expression)
    {
        if ($expression) {
            return '<?php '.$expression.'; ?>';
        }
        return '@php';
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    protected function csrfCompile()
    {
        return csrf_field();
    }

    /**
     * @param $expression
     * @return void
     */
    protected function extendsCompile($expression)
    {
        $this->footer[] = $this->includeCompile($expression);
    }

    /**
     * @param $expression
     * @return string
     */
    protected function urlCompile($expression)
    {
        return '<?php echo url'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function oldCompile($expression)
    {
        return '<?php echo old'.$expression.'; ?>';
    }

    /**
     * @return string
     */
    protected function breakCompile()
    {
        return '<?php break; ?>';
    }

    /**
     * @return string
     */
    protected function continueCompile()
    {
        return '<?php continue; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function includeCompile($expression)
    {
        $expression = $this->stripParentheses($expression);
        return '<?php echo $this->render('.$expression.', get_defined_vars()); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function printCompile($expression)
    {
        return '<?php print_r'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function dumpCompile($expression)
    {
        return '<?php var_dump'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function yieldCompile($expression)
    {
        return '<?php echo $this->yieldContent'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function sectionCompile($expression)
    {
        return '<?php $this->startSection'.$expression.'; ?>';
    }

    /**
     * @return string
     */
    protected function endsectionCompile()
    {
        return '<?php $this->endSection(); ?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function methodCompile($expression)
    {
        return '<?php echo method_field'.$expression.'?>';
    }

    /**
     * @param $expression
     * @return string
     */
    protected function routeCompile($expression)
    {
        return '<?php echo Route::getName'.$expression.'; ?>';
    }

    /**
     * @param $expression
     * @return bool|string
     */
    protected function stripParentheses($expression)
    {
        if (strpos($expression, '(') !== false) {
            $expression = substr($expression, 1, -1);
        }
        return $expression;
    }
}
