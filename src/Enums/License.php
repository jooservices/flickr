<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum License: int
{
    case All = 0;
    case Attribution = 4;
    case AttributionShareAlike = 5;
    case AttributionNoDerivs = 6;
    case AttributionNonCommercial = 1;
    case AttributionNonCommercialShareAlike = 2;
    case AttributionNonCommercialNoDerivs = 3;
    case NoKnownCopyright = 7;
    case PublicDomain = 8;
}
