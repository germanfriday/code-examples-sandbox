//
//  NewsItem.h
//  RSS Reader
//
//  Created by Ben on 17/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface NewsItem : NSObject {
	NSString * title;
	NSString * description;
	NSURL * link;
}

@property (retain) NSString * title;
@property (retain) NSString * description;
@property (retain) NSURL * link;

@end
