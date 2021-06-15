//
//  Button_FunViewController.h
//  Button Fun
//
//  Created by Chris Freitag on 3/22/10.
//  Copyright __MyCompanyName__ 2010. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface Button_FunViewController : UIViewController {
	IBOutlet	UILabel	*statusText;

}
@property	(retain, nonatomic) UILabel *statusText;
- (IBAction)buttonPressed:(id)sender;
@end

