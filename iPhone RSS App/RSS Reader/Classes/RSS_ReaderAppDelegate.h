//
//  RSS_ReaderAppDelegate.h
//  RSS Reader
//
//  Created by Ben on 17/08/2009.
//  Copyright __MyCompanyName__ 2009. All rights reserved.
//

@interface RSS_ReaderAppDelegate : NSObject <UIApplicationDelegate> {
    
    UIWindow *window;
    UINavigationController *navigationController;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) IBOutlet UINavigationController *navigationController;

@end

