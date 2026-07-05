<?php

declare(strict_types=1);

namespace JOOservices\Flickr\Enums;

enum PhotoExtra: string
{
    case Description = 'description';
    case License = 'license';
    case DateUpload = 'date_upload';
    case DateTaken = 'date_taken';
    case OwnerName = 'owner_name';
    case IconServer = 'icon_server';
    case OriginalFormat = 'original_format';
    case LastUpdate = 'last_update';
    case Geo = 'geo';
    case Tags = 'tags';
    case MachineTags = 'machine_tags';
    case O_dims = 'o_dims';
    case Views = 'views';
    case PathAlias = 'path_alias';
    case UrlSquare = 'url_sq';
    case UrlThumb = 'url_t';
    case UrlSmall = 'url_s';
    case UrlMedium = 'url_m';
    case UrlLarge = 'url_l';
    case UrlOriginal = 'url_o';
}
