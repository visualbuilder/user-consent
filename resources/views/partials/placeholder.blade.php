<x-dynamic-component
    :component="$getFieldWrapperView()"
    :has-inline-label="method_exists($this, 'hasInlineLabel') ? $hasInlineLabel() : false"
    :id="$getId()"
    :label="method_exists($this, 'getLabel') ? $getLabel() : ''"
    :label-sr-only="method_exists($this, 'isLabelHidden') ? $isLabelHidden() : false"
    :helper-text="method_exists($this, 'getHelperText') ? $getHelperText() : null"
    :hint="method_exists($this, 'getHint') ? $getHint() : null"
    :hint-color="method_exists($this, 'getHintColor') ? $getHintColor() : null"
    :hint-icon="method_exists($this, 'getHintIcon') ? $getHintIcon() : null"
    :hint-icon-tooltip="method_exists($this, 'getHintIconTooltip') ? $getHintIconTooltip() : null"
    :state-path="method_exists($this, 'getStatePath') ? $getStatePath() : ''"
>
    <div
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class(['fi-fo-placeholder text-base leading-6'])
        }}
    >
        {{ method_exists($this, 'getContent') ? $getContent() : ($getState() ?? '') }}
    </div>
</x-dynamic-component>
