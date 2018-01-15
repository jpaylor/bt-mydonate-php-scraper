<?php

namespace JPaylor\BTMyDonate\Models;

/**
 * Fundraising Model
 *
 * @package JPaylor\BTMyDonate\Models
 */
class Fundraising {
    /**
     * @var float total raised
     */
    public $totalRaised;

    /**
     * @var float target
     */
    public $target;

    /**
     * @return float
     */
    public function getTotalRaised()
    {
        return $this->totalRaised;
    }

    /**
     * @param float $totalRaised
     * @return Fundraising
     */
    public function setTotalRaised($totalRaised)
    {
        $this->totalRaised = $totalRaised;
        return $this;
    }

    /**
     * @return float
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param float $target
     * @return Fundraising
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }


}