<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

trait Events
{
    /**
     * @var array
     */
    protected $hooks = [
        'creating'  => [],
        'editing'   => [],
        'submitted' => [],
        'saving'    => [],
        'saved'     => [],
        'deleting'  => [],
        'deleted'   => [],
    ];

    /**
     * Set after getting creating model callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function creating(Closure $callback)
    {
        $this->hooks['creating'][] = $callback;
    }

    /**
     * Set after getting editing model callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function editing(Closure $callback)
    {
        $this->hooks['editing'][] = $callback;
    }

    /**
     * Set submitted callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function submitted(Closure $callback)
    {
        $this->hooks['submitted'][] = $callback;
    }

    /**
     * Set saving callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function saving(Closure $callback)
    {
        $this->hooks['saving'][] = $callback;
    }

    /**
     * Set saved callback.
     *
     * @param Closure $callback
     *
     * @return void
     */
    public function saved(Closure $callback)
    {
        $this->hooks['saved'][] = $callback;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function deleting(Closure $callback)
    {
        $this->hooks['deleting'][] = $callback;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function deleted(Closure $callback)
    {
        $this->hooks['deleted'][] = $callback;
    }

    /**
     * Call creating callbacks.
     *
     * @return mixed
     */
    protected function callCreating()
    {
        return $this->callListeners('creating');
    }

    /**
     * Call editing callbacks.
     *
     * @return mixed
     */
    protected function callEditing()
    {
        return $this->callListeners('editing');
    }

    /**
     * Call submitted callback.
     *
     * @return mixed
     */
    protected function callSubmitted()
    {
        return $this->callListeners('submitted');
    }

    /**
     * Call saving callback.
     *
     * @return mixed
     */
    protected function callSaving()
    {
        return $this->callListeners('saving');
    }

    /**
     * Callback after saving a Model.
     *
     * @return mixed|null
     */
    protected function callSaved()
    {
        return $this->callListeners('saved');
    }

    /**
     * @return  mixed|null
     */
    protected function callDeleting()
    {
        return $this->callListeners('deleting');
    }

    /**
     * @return  mixed|null
     */
    protected function callDeleted()
    {
        return $this->callListeners('deleted');
    }

    /**
     * @param array $listeners
     * @return mixed|void
     */
    protected function callListeners($name)
    {
        foreach ($this->hooks[$name] as $func) {
            $this->model && $func->bindTo($this->model);

            if (($ret = $func($this)) instanceof Response) {
                if ($ret instanceof RedirectResponse && $this->isAjaxRequest()) {
                    return;
                }

                return $ret;
            }
        }
    }
}
