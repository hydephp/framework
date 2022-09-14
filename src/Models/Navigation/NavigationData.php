<?php

namespace Hyde\Framework\Models\Navigation;

use Hyde\Framework\Concerns\JsonSerializesArrayable;
use Hyde\Framework\Contracts\FrontMatter\Support\NavigationSchema;
use Illuminate\Contracts\Support\Arrayable;

final class NavigationData extends \ArrayObject implements NavigationSchema, Arrayable, \JsonSerializable
{
    use JsonSerializesArrayable;

    public ?string $label = null;
    public ?string $group = null;
    public ?bool $hidden = null;
    public ?int $priority = null;

    public function __construct(?string $label = null, ?string $group = null, ?bool $hidden = null, ?int $priority = null)
    {
        $this->label = $label;
        $this->group = $group;
        $this->hidden = $hidden;
        $this->priority = $priority;

        parent::__construct($this->toArray());
    }

    public static function make(array $data): self
    {
        return new self(
            $data['label'] ?? null,
            $data['group'] ?? null,
            $data['hidden'] ?? null,
            $data['priority'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'group' => $this->group,
            'hidden' => $this->hidden,
            'priority' => $this->priority,
        ];
    }
}
