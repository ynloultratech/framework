# Install

@TODO: complete this documentation

YnloFramework change the way you need register bundles to start developing.
After create new blank symfony application, override the `registerBundles` in your kernel 
with something like:

````php
  public function registerBundles()
    {
        $bundles = YnloFramework\KernelBuilder::setUp($this)
            ->basicApplication()
            ->build();

        return $bundles;
    }

````

The above statements register all bundles to start using YnloFramework with symfony. 
The KernelBuilder provided by the framework is a helper to customize your kernel.

### Examples

Compile a basic application with doctrine support:
````php
 $bundles = YnloFramework\KernelBuilder::setUp($this)
            ->basicApplication()
            ->withDoctrine()
            ->build();
```` 
   
Compile a basic application with administration, user authentication and doctrine support:
````php
 $bundles = YnloFramework\KernelBuilder::setUp($this)
            ->basicApplicationWithAdmin()
            ->build();
```` 

Adding other bundles:
````php
 $bundles = YnloFramework\KernelBuilder::setUp($this)
            ->basicApplicationWithAdmin()
            ->addBundle(new MyCustomBundle())
            ->build();
```` 

> NOTE: by default the kernel compile the AppBundle if exists.
