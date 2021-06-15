//
//  WebVC.h
//  RSS Reader
//
//  Created by Ben on 19/08/2009.
//  Copyright 2009 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
@class NewsItem;

@interface WebVC : UIViewController {
	IBOutlet UIWebView *storyWebView;
	NewsItem * storyNewsItem;
}

@property (retain) NewsItem * storyNewsItem;

@end
