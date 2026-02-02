<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public $options;        // Data source (formerly rs/values)
    public $name;           // input name
    public $selected;       // selected value
    public $default_value;  // 'all' value
    public $default_label;  // 'ALL YEARS' label
    public $valueKey;       // Key for option value (e.g., 'id')
    public $labelKey;       // Key for option text (e.g., 'status_name')
    public $countKey;

    public function __construct(
        $options = [],
        $name = 'dropdown',
        $selected = null,
        $allValue = 'all',
        $allDisplay = 'All',
        $valueKey = null,    // If null, we assume it's a simple array (like years)
        $labelKey = null,
        $countKey = null
    ) {
        $this->options = $options;
        $this->name = $name;
        $this->selected = $selected;
        $this->default_value = $allValue;
        $this->default_label = $allDisplay;
        $this->valueKey = $valueKey;
        $this->labelKey = $labelKey;
        $this->countKey = $countKey;
    }

    public function render(): View|Closure|string
    {
        return view('components.dropdown');
    }
}
