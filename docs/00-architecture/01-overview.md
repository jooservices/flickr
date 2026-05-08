# Overview

`jooservices/flickr` is a pure PHP SDK. The runtime flow is:

User code -> `Flickr` -> service -> DTO/request parameters -> method registry -> Flickr client/upload client -> OAuth signer/token store -> `jooservices/client` transport -> Flickr.

Laravel integration is out of scope and belongs in a future separate package.
