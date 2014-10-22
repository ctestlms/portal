 
import java.util.*;
import java.math.*;
public class shannon
{
private	String sh;
private int arr1[];
private int charec[];
private int arr[];
private int asc[];
private int size;
public shannon()  //CONSTRUCTOR
  {
  	
	arr1=new int[100];
	charec=new int[100];
	arr=new int[100];
	asc=new int[100];
	}
	public void test()
	{
		int c=0;
		for(int i=0;i<size;i++)
		if(arr1[i]==0){break;}
		else
		asc[c++]=arr1[i];
		}

///////////////////////////////////////
///////////////////////////
/*
public int getMid(int start,int end)
{
	int sum=0;
 for(int i=start;i<=end;i++)
  sum += arr1[i];
 int mid= sum/2;
 int midindex=start;
 int currsum=arr1[midindex];
  while(currsum<mid)
  {
   midindex++;
   currsum+=freq[midindex];
  }
return midindex;
}*/
////////////////////////////////////////////////
///////////////////////////////////ENTROPY/////////////
		public void entropy()
		{
		
			
			double sum=0,a1=0;
			int i=0;
			do{
				a1=arr1[i];
                                  a1=a1/size;
				
			sum=(sum-(a1)*Math.log((a1)/Math.log(2)));
			i++;
			
		}while(asc[i]!=0);
		System.out.println("Entropy "+sum);
	}
public void ascending()
{
	for (int i=0; i < size; i++)
		for (int j = size-1; j>i; j--)
		{
			if(arr1[j] > arr1[j-1] )
			{
				int temp = arr1[j];
				arr1[j] = arr1[j-1];
				arr1[j-1] = temp;
			}
		} 
		for(int q=0;q<size;q++)
		if(arr1[q]==0){}else
	System.out.println(""+arr1[q]);
		
}
	
public void input()
{

	int count;
	Scanner in=new Scanner(System.in);
	System.out.println("Input the String  ");
	sh=in.nextLine();
	size=sh.length();
	char [] arr=sh.toCharArray( );
	
	for( int i=0;i<size;i++)
	{
		count=0;
		for(int j=0;j<size;j++)
		{ 
		
		
		if(arr[i]==charec[j])
		{break;}
		else
		
		
		{
			
			if(arr[i]==arr[j])
			{
				
				charec[i]=arr[i];
				count++;
				arr1[i]=count;
				}
			
			}//inner loop
		
		}
		}//1st loop
	
	}
	/////////////////////////////////////////////
	//////////////////////////////////////////
	/////////////////////////////////////
	public void display()
	{
		for(int i=0;i<30;i++)
		{//try{
		     if(arr1[i]==0){}
		     else	
			System.out.println("character     "+charec[i]+"   repeats  "+arr1[i]);
		//}catch(NullPointerException e){}
			
			}
			//probability
			for(int i=0;i<30;i++)
		{
			if(arr1[i]==0){}
		     else
			System.out.println("character     "+charec[i]+"   probability  "+arr1[i]+"/"+size);
			
			
			}
			
		}

public static void main(String args[])
{
	
	shannon s1=new shannon();
	s1.input();
	s1.display();
	s1.ascending();
	s1.test();
	s1.entropy();
	 
		
			
			
	} 
}

