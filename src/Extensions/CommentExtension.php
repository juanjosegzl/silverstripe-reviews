<?php

namespace ilateral\SilverStripe\Reviews\Extensions;

use SilverStripe\ORM\DataExtension;
use ilateral\SilverStripe\Reviews\Helpers\ReviewHelper;
use SilverStripe\Forms\FieldList;
use ilateral\SilverStripe\Reviews\Control\ReviewsController;

class CommentExtension extends DataExtension
{
    private static $db = [
        'Rating' => 'Int'
    ];

    private static $casting = [
        'MaxRating' => 'Int',
        'RatingStars' => 'HTMLText',
        'ExcessStars' => 'HTMLText'
    ];

    private static $summary_fields = [
        'Rating'
    ];

    public function getMaxRating()
    {
        return $this->getOwner()->Parent()->getCommentsOption('max_rating');
    }

    /**
     * Get the rating as HTML Star characters
     * (one star per increment of rating).
     * 
     * @return string
     */
    public function getRatingStars()
    {
        return ReviewHelper::getStarsFromValues(
            $this->getOwner()->Parent()->getCommentsOption('min_rating'),
            round($this->getOwner()->Rating)
        );
    }

    /**
     * Get the excess rating as HTML Star characters
     * (one star per increment of rating).
     * 
     * @return string
     */
    public function getExcessStars()
    {
        $max = $this->getOwner()->Parent()->getCommentsOption("max_rating");
        $rating = $this->getOwner()->Rating;
        $excess = $max - round($rating);

        return ReviewHelper::getStarsFromValues(
            $this->getOwner()->Parent()->getCommentsOption("min_rating"),
            $excess,
            $html = "&#9734;"
        );
    }

    public function updateCMSFields(FieldList $fields)
    {
        /** @var \SilverStripe\Comments\Model\Comment */
        $owner = $this->getOwner();

        $fields->insertBefore(
            'Name',
            $owner->dbObject('Rating')->scaffoldFormField($owner->fieldLabel('Rating'))
        );
    }

    public function updateController() {
        return ReviewsController::create();
    }
}
